<?php

namespace App\Http\Controllers\Dashboards;

use App\Helper\Workers\WorkerHelper;
use App\Http\Controllers\Controller;
use App\Models\Client\Client;
use App\Models\Client\ClientJob;
use App\Models\Client\Site;
use App\Models\Group\CostCentre;
use App\Models\Job\JobShift;
use App\Models\Job\JobShiftWorker;
use App\Models\Job\PayrollLineItem;
use App\Models\Payroll\PayrollWeekDate;
use App\Models\Timesheet\Timesheet;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class DashboardController extends Controller
{
    use JsonResponse;
    public function index() {
        $previousPayrollWeek = PayrollWeekDate::query()
            ->where('pay_date', '<=', Carbon::now()->toDateString())
            ->latest('pay_date')
            ->first();
        return view('dashboards/dashboard', compact(['previousPayrollWeek']));
    }

    public function getDashboardData(Request $request) {
        try {
            $cost_center = $request->input('cost_center');

            return self::responseWithSuccess('Dashboard Data', [
                'alert_section' => $this->alert($cost_center),
                'week_snapshot_section' => $this->weekSnapshot($cost_center, $request->input('payroll_week')),
                'booking_section' => $this->bookings($cost_center),
                'shift_and_hours_trends' => $this->shift_and_hours_trends($cost_center),
                'worker_plus_section' => $this->workerPlus($cost_center),
                'top_client_section' => $this->topClient($cost_center, $request->input('payroll_week')),
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function alert($cost_center) {
        $startDate = Carbon::today()->toDateString();

        //Shifts with spaces in tomorrow
        //$shiftSpaceTomorrow = Carbon::today()->addDay(1)->toDateString();

        $after1Hour = Carbon::now()->addHour();
        $in36Hours = Carbon::now()->addHours(36);

        $shiftWithSpaceTomorrow = JobShift::query()
            //->whereBetween('date', [$startDate, $shiftSpaceTomorrow])
            ->whereRaw("STR_TO_DATE(CONCAT(`date`, ' ', `start_time`), '%Y-%m-%d %H:%i:%s') > ?", [$after1Hour])
            ->whereRaw("STR_TO_DATE(CONCAT(`date`, ' ', `start_time`), '%Y-%m-%d %H:%i:%s') < ?", [$in36Hours])
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('client_job_details.site_details', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->with(['JobShiftWorker_details', 'client_job_details'])
            ->get()
            ->map(function ($jobShift) {
                if (count($jobShift->JobShiftWorker_details) < $jobShift->number_workers) {
                    return $jobShift;
                }
                return null;
            })
            ->filter()
            ->count();

        //Shifts with spaces in next 7 days
        $shiftSpaceEndDate = Carbon::today()->addDay(7)->toDateString();
        $shiftWithSpace = JobShift::query()
            ->whereBetween('date', [$startDate, $shiftSpaceEndDate])
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('client_job_details.site_details', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->with(['JobShiftWorker_details', 'client_job_details'])
            ->get()
            ->map(function ($jobShift) {
                if (count($jobShift->JobShiftWorker_details) < $jobShift->number_workers) {
                    return $jobShift;
                }
                return null;
            })
            ->filter()
            ->count();

        //BOOKING INVITATIONS TO CHASE (<7 DAYS)
        $bookingInvitationEndDate = Carbon::today()->addDay(7)->toDateString();
        $bookingInvitationChase = JobShiftWorker::query()
            ->whereBetween('invited_at', [$startDate, $bookingInvitationEndDate])
            ->whereNull('confirmed_at')
            ->whereNull('declined_at')
            ->whereNull('cancelled_at')
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('jobShift.client_job_details.site_details', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->with('jobShift')
            ->count();

        //Expiring RTWs in next 4 weeks
        $alertEndDate = Carbon::today()->addDay(28)->toDateString();

        $expiringRtws = Worker::query()->select('id')
            ->where('status', 'Active')
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('worker_cost_center', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->whereHas('rights_to_work_details', function ($query) use ($startDate, $alertEndDate)  {
                $query->whereBetween('end_date', [$startDate, $alertEndDate])->latest('end_date');
            })
            ->with(['worker_cost_center', 'rights_to_work_details'])
            ->count();

        //Shift workers without payroll refs
        $shiftWorkersWithoutPayroll = Timesheet::query()
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('worker_details.worker_cost_center', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->whereHas('worker_details', function ($subQuery) {
                $subQuery->whereNull('payroll_reference')
                    ->where('status', 'Active');
            })
            ->with('worker_details')
            ->get()
            ->groupBy('worker_id')
            ->count();

        //Workers have worked >12 days in a row
        $workersWorkedGreaterThan12Days = Timesheet::query()
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('worker_details.worker_cost_center', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->with('worker_details')
            ->get()
            ->groupBy('worker_id')
            ->filter(function ($timesheets) {
                $sortedDates = $timesheets->sortBy('date')->pluck('date')->map(function ($date) {
                    return Carbon::parse($date);
                });

                $consecutiveDays = 1;
                foreach ($sortedDates as $index => $date) {
                    if (isset($sortedDates[$index + 1]) && $date->diffInDays($sortedDates[$index + 1]) === 1) {
                        $consecutiveDays++;
                        if ($consecutiveDays >= 12) {
                            return true;
                        }
                    } else {
                        $consecutiveDays = 1;
                    }
                }
                return false;
            });

        return [
            'shift_with_space_tomorrow' => $shiftWithSpaceTomorrow,
            'shift_with_space' => $shiftWithSpace,
            'booking_invitation_chase' => $bookingInvitationChase,
            'expiring_rtws' => $expiringRtws,
            'shift_workers_without_payroll' => $shiftWorkersWithoutPayroll,
            'workers_worked_greater_than_12_days' => $workersWorkedGreaterThan12Days->count(),
        ];
    }

    private function weekSnapshot($cost_center, $payroll_week) {
        $explode = explode('-', $payroll_week);
        $pw_payroll_week = ($explode[0] != 1) ? $explode[0] - 1 : $explode[0];
        $pw_payroll_week = $pw_payroll_week.'-'.$explode[1];

        /*--- NEXT 7 DAYS TIMESHEET ENTRIES ---*/
        $payroll_week_data = PayrollWeekDate::query()
            ->where('payroll_week_number', $explode[0])
            ->where('year', $explode[1])
            ->first();

        /*$start_date = Carbon::parse($payroll_week_data['pay_date']);
        $end_date = $start_date->copy()->addDays(6);*/
        $start_date = Carbon::parse($payroll_week_data['monday_payroll_start']);
        $end_date = Carbon::parse($payroll_week_data['monday_payroll_end']);

        $timesheetQuery = Timesheet::query()
            ->whereBetween('date', [$start_date->toDateString(), $end_date->toDateString()])
            ->whereNotNull('locked_at')
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('job_details.site_details', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            });
        $timesheetEntry = $timesheetQuery->with('job_details')->count();
        $dayWiseTimesheetEntry = $timesheetQuery->selectRaw('DATE(date) as day, COUNT(*) as count')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('count', 'day')
            ->toArray();

        $totalTimesheetDayWuse = [];
        foreach (range(0, 6) as $offset) {
            $date = $start_date->copy()->addDays($offset)->toDateString();
            $totalTimesheetDayWuse[] = $dayWiseTimesheetEntry[$date] ?? 0;
        }

        /*--- LAST 7 DAYS TIMESHEET ENTRIES ---*/
        $pw_start_date = $start_date->copy()->subDay(6);
        $pw_timesheetEntry = Timesheet::query()
            ->whereBetween('date', [$pw_start_date->toDateString(), $start_date->toDateString()])
            ->whereNotNull('locked_at')
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('job_details.site_details', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->with('job_details')
            ->count();

        /*--- BEGIN CURRENT WEEK DATA ---*/
        $payrollLineItem = PayrollLineItem::query()
            ->whereNot('pay_rate_name', 'Bonus')
            ->where('payroll_week', $payroll_week)
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('site_details', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->with('site_details')
            ->get()
            ->toArray();

        if ($payrollLineItem) {
            $hours = array_sum(array_column($payrollLineItem, 'total_hours'));
            $charge = array_sum(array_column($payrollLineItem, 'total_charge'));
            $paid = array_sum(array_column($payrollLineItem, 'total_pay'));

            $avg_charge = $charge / $hours;
            $avg_pay = $paid / $hours;
            $avg_margin = ($avg_charge / $avg_pay * 100) - 100;
        } else {
            $hours = 0;
            $charge = 0;
            $paid = 0;
            $avg_charge = 0;
            $avg_pay = 0;
            $avg_margin = 0;
        }

        $totalHoursLoggedDayWise = [];
        $totalChargedDayWise = [];
        $totalPayDayWise = [];
        foreach (range(1, 7) as $day) {
            $column = "day_{$day}_hours";
            $totalHoursLoggedDayWise[] = array_sum(array_column($payrollLineItem, $column));

            $totalChargedDayWise[] = array_sum(array_map(function ($item) use ($column) {
                return $item[$column] * $item['charge_rate'];
            }, $payrollLineItem));

            $totalPayDayWise[] = array_sum(array_map(function ($item) use ($column) {
                return $item[$column] * $item['pay_rate'];
            }, $payrollLineItem));
        }
        /*--- END CURRENT WEEK DATA ---*/

        /*--- BEGIN PREVIOUS WEEK DATA ---*/
        $pw_payrollLineItem = PayrollLineItem::query()
            ->whereNot('pay_rate_name', 'Bonus')
            ->where('payroll_week', $pw_payroll_week)
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('site_details', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->with('site_details')
            ->get()
            ->toArray();

        if ($pw_payrollLineItem) {
            $pw_hours = array_sum(array_column($pw_payrollLineItem, 'total_hours'));
            $pw_charge = array_sum(array_column($pw_payrollLineItem, 'total_charge'));
            $pw_paid = array_sum(array_column($pw_payrollLineItem, 'total_pay'));

            $pw_avg_charge = $pw_charge / $pw_hours;
            $pw_avg_pay = $pw_paid / $pw_hours;
            $pw_avg_margin = $pw_avg_charge - $pw_avg_pay;
        } else {
            $pw_hours = 0;
            $pw_charge = 0;
            $pw_paid = 0;

            $pw_avg_charge = 0;
            $pw_avg_pay = 0;
            $pw_avg_margin = 0;
        }
        /*--- END PREVIOUS WEEK DATA ---*/

        /*--- COUNT AVERAGE ---*/
        $avg_charge_difference = $avg_charge - $pw_avg_charge;
        $avg_pay_difference = $avg_pay - $pw_avg_pay;
        $avg_margin_difference = ($pw_avg_margin != 0) ? ($avg_margin / $pw_avg_margin * 100) - 100 : 100;

        return [
            'shifts' => $timesheetEntry,
            'hours' => $hours,
            'charged' => number_format($charge, 2),
            'paid' => number_format($paid, 2),

            'timesheet_different' => WorkerHelper::preparedBookingDifference($timesheetEntry, $pw_timesheetEntry),
            'hours_different' => WorkerHelper::preparedBookingDifference($hours, $pw_hours),
            'charge_different' => WorkerHelper::preparedBookingDifference($charge, $pw_charge),
            'paid_different' => WorkerHelper::preparedBookingDifference($paid, $pw_paid),

            'avg_charge' => number_format($avg_charge, 2),
            'avg_pay' => number_format($avg_pay, 2),
            'avg_margin' => number_format($avg_margin, 2),

            'charge_difference' => WorkerHelper::preparedSnapShotDifference($avg_charge_difference),
            'pay_difference' => WorkerHelper::preparedSnapShotDifference($avg_pay_difference),
            'margin_difference' => str_replace('£', '', WorkerHelper::preparedSnapShotDifference($avg_margin_difference)).'%',

            'pay_date' => date('d-m-Y', strtotime($payroll_week_data['pay_date'])),

            'total_timesheet_day_wise' => $totalTimesheetDayWuse,
            'total_hours_logged_day_wise' => $totalHoursLoggedDayWise,
            'total_charged_day_wise' => $totalChargedDayWise,
            'total_pay_day_wise' => $totalPayDayWise
        ];
    }

    private function bookings($cost_center) {
        $startDate = Carbon::today()->toDateString();
        $endDate = Carbon::today()->addDay(7)->toDateString();
        $preStartDate = Carbon::today()->subDays(7)->toDateString();

        //Chart
        $jobShiftWorkerChart = [];
        $shiftChart = [];
        $jobChart = [];
        $siteChart = [];
        $clientChart = [];
        $jobNumber = [];
        $siteNumber = [];
        $clientNumber = [];

        for ($date = Carbon::parse($startDate); $date->lte(Carbon::parse($endDate)); $date->addDay()) {
            $filter_date = $date->toDateString();

            /*$jobShiftWorkerChart[] = JobShiftWorker::query()
                ->whereDate('shift_date', $filter_date)
                ->whereNull('cancelled_at')
                ->whereNull('declined_at')
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->whereHas('jobShift.client_job_details.site_details', function ($subQuery) use ($cost_center) {
                        $subQuery->where('cost_center', $cost_center);
                    });
                })
                ->with('jobShift')
                ->count();

            $shiftChart[] = JobShift::query()
                ->whereDate('date', $filter_date)
                ->whereNull('cancelled_at')
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->whereHas('client_job_details.site_details', function ($subQuery) use ($cost_center) {
                        $subQuery->where('cost_center', $cost_center);
                    });
                })
                ->with('client_job_details')
                ->count();

            $jobChart[] = ClientJob::query()
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->whereHas('site_details', function ($subQuery) use ($cost_center) {
                        $subQuery->where('cost_center', $cost_center);
                    });
                })
                ->whereHas('job_shift_details', function ($query) use ($filter_date)  {
                    $query->whereDate('date', $filter_date)
                        ->whereNull('cancelled_at');
                })
                ->with(['site_details', 'job_shift_details'])
                ->count();

            $siteChart[] = Site::query()
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->where('cost_center', $cost_center);
                })
                ->whereHas('job_details.job_shift_details', function ($query) use ($filter_date)  {
                    $query->whereDate('date', $filter_date)
                        ->whereNull('cancelled_at');
                })
                ->with(['job_details'])
                ->count();

            $clientChart[] = Client::query()
                ->whereHas('client_job_details.job_shift_details', function ($query) use ($filter_date)  {
                    $query->whereDate('date', $filter_date)
                        ->whereNull('cancelled_at');
                })
                ->with('client_job_details')
                ->count();*/

            $jobShiftWorker = JobShiftWorker::query()
                ->whereDate('shift_date', $filter_date)
                ->whereNotNull('confirmed_at')
                ->whereNull('cancelled_at')
                ->whereNull('declined_at')
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->whereHas('jobShift.client_job_details.site_details', function ($subQuery) use ($cost_center) {
                        $subQuery->where('cost_center', $cost_center);
                    });
                })
                ->with('jobShift')
                ->get()
                ->toArray();
            $jobShiftWorkerChart[] = count($jobShiftWorker);

            $shiftChart[] = JobShift::query()
                ->whereIn('id', array_column($jobShiftWorker, 'job_shift_id'))
                ->count();

            $jobChart[] = ClientJob::query()
                ->whereIn('id', array_column(array_column($jobShiftWorker, 'job_shift'), 'job_id'))
                ->count();

            $siteChart[] = Site::query()
                ->whereIn('id', array_column(array_column(array_column($jobShiftWorker, 'job_shift'), 'client_job_details'), 'site_id'))
                ->count();

            $clientChart[] = Client::query()
                ->whereIn('id', array_column(array_column(array_column($jobShiftWorker, 'job_shift'), 'client_job_details'), 'client_id'))
                ->count();

            $jobNumber[] = ClientJob::query()
                ->whereIn('id', array_column(array_column($jobShiftWorker, 'job_shift'), 'job_id'))
                ->get();

            $siteNumber[] = Site::query()
                ->whereIn('id', array_column(array_column(array_column($jobShiftWorker, 'job_shift'), 'client_job_details'), 'site_id'))
                ->get();

            $clientNumber[] = Client::query()
                ->whereIn('id', array_column(array_column(array_column($jobShiftWorker, 'job_shift'), 'client_job_details'), 'client_id'))
                ->get();
        }
        //return $jobShiftWorkerChart;

        //Next 7 Days Shift / Job / Site / Client Data
        $jobShiftWorker = array_sum($jobShiftWorkerChart);
        $shifts = array_sum($shiftChart);
        $job = collect($jobNumber)->flatten(1)->pluck('id')->unique()->count();
        $site =  collect($siteNumber)->flatten(1)->pluck('id')->unique()->count();
        $client = collect($clientNumber)->flatten(1)->pluck('id')->unique()->count();

        /*$job = ClientJob::query()
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('site_details', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->whereHas('job_shift_details', function ($query) use ($startDate, $endDate)  {
                $query->whereBetween('date', [$startDate, $endDate])
                    ->whereNull('cancelled_at');
            })
            ->with(['site_details', 'job_shift_details'])
            ->count();

        $site = Site::query()
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->where('cost_center', $cost_center);
            })
            ->whereHas('job_details.job_shift_details', function ($query) use ($startDate, $endDate)  {
                $query->whereBetween('date', [$startDate, $endDate])
                    ->whereNull('cancelled_at');
            })
            ->with(['job_details'])
            ->count();

        $client =  Client::query()
            ->whereHas('client_job_details.job_shift_details', function ($query) use ($startDate, $endDate)  {
                $query->whereBetween('date', [$startDate, $endDate])
                    ->whereNull('cancelled_at');
            })
            ->with('client_job_details')
            ->count();*/

        //Previous 7 Days Shift / Job / Site / Client Data
        $pre_job_shift_worker = JobShiftWorker::query()
            ->whereBetween('shift_date', [$preStartDate, $startDate])
            ->whereNotNull('confirmed_at')
            ->whereNull('cancelled_at')
            ->whereNull('declined_at')
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('jobShift.client_job_details.site_details', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->with('jobShift')
            ->get()
            ->toArray();

        $preJobShiftWorker = count($pre_job_shift_worker);
        $preShift = JobShift::query()
            ->whereIn('id', array_column($pre_job_shift_worker, 'job_shift_id'))
            ->count();

        $preJob = ClientJob::query()
            ->whereIn('id', array_unique(array_column(array_column($pre_job_shift_worker, 'job_shift'), 'job_id')))
            ->count();

        $preSite = Site::query()
            ->whereIn('id', array_unique(array_column(array_column(array_column($pre_job_shift_worker, 'job_shift'), 'client_job_details'), 'site_id')))
            ->count();

        $preClient = Client::query()
            ->whereIn('id', array_unique(array_column(array_column(array_column($pre_job_shift_worker, 'job_shift'), 'client_job_details'), 'client_id')))
            ->count();

        /*$preJobShiftWorker = JobShiftWorker::query()
            ->whereBetween('shift_date', [$preStartDate, $startDate])
            ->whereNull('cancelled_at')
            ->whereNull('declined_at')
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('jobShift.client_job_details.site_details', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->with('jobShift')
            ->count();

        $preShift = JobShift::query()
            ->whereBetween('date', [$preStartDate, $startDate])
            ->whereNull('cancelled_at')
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('client_job_details.site_details', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->with('client_job_details')
            ->count();

        $preJob = ClientJob::query()
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('site_details', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->whereHas('job_shift_details', function ($query) use ($preStartDate, $startDate)  {
                $query->whereBetween('date', [$preStartDate, $startDate])
                    ->whereNull('cancelled_at');
            })
            ->with(['site_details', 'job_shift_details'])
            ->count();

        $preSite = Site::query()
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->where('cost_center', $cost_center);
            })
            ->whereHas('job_details.job_shift_details', function ($query) use ($preStartDate, $startDate)  {
                $query->whereBetween('date', [$preStartDate, $startDate])
                    ->whereNull('cancelled_at');
            })
            ->with(['job_details'])
            ->count();

        $preClient = Client::query()
            ->whereHas('client_job_details.job_shift_details', function ($query) use ($preStartDate, $startDate)  {
                $query->whereBetween('date', [$preStartDate, $startDate])
                    ->whereNull('cancelled_at');
            })
            ->with('client_job_details')
            ->count();*/

        return [
            'job_shift_worker' => number_format($jobShiftWorker),
            'shifts' => number_format($shifts),
            'job' => number_format($job),
            'site' => number_format($site),
            'client' => number_format($client),

            'job_shift_worker_difference' => WorkerHelper::preparedBookingDifference($jobShiftWorker, $preJobShiftWorker),
            'shift_difference' => WorkerHelper::preparedBookingDifference($shifts, $preShift),
            'job_difference' => WorkerHelper::preparedBookingDifference($job, $preJob),
            'site_difference' => WorkerHelper::preparedBookingDifference($site, $preSite),
            'client_difference' => WorkerHelper::preparedBookingDifference($client, $preClient),

            'job_shift_worker_chart' => $jobShiftWorkerChart,
            'shift_chart' => $shiftChart,
            'job_chart' => $jobChart,
            'site_chart' => $siteChart,
            'client_chart' => $clientChart,
        ];
    }

    private function shift_and_hours_trends($cost_center) {
        $currentPayrollWeek = PayrollWeekDate::query()->select('payroll_week_number')
            ->where('pay_date', '<=', Carbon::now()->toDateString())
            ->latest('pay_date')
            //->whereDate(strtolower(Carbon::now()->format('l')).'_payroll_start', Carbon::today()->toDateString())
            ->first();
        $currentWeekNumber = $currentPayrollWeek->payroll_week_number;
        $previousWeeks = range($currentWeekNumber - 4, $currentWeekNumber);

        $payrollWeek = PayrollWeekDate::query()
            ->whereIn('payroll_week_number', $previousWeeks)
            ->where('year', date('Y'))
            ->get();
        //$payrollWeek = PayrollWeekDate::query()->whereIn('payroll_week_number', [34,35,36,37,38])->get();

        $label = [];
        $shift_data = [];
        $hours_data = [];
        foreach ($payrollWeek as $row) {

            /*$start_date = Carbon::parse($row['pay_date']);
            $end_date = $start_date->copy()->addDays(7);*/
            $start_date = Carbon::parse($row['monday_payroll_start']);
            $end_date = Carbon::parse($row['monday_payroll_end']);

            /*$timesheetEntry = Timesheet::query()
                ->whereBetween('date', [$start_date->toDateString(), $end_date->toDateString()])
                ->whereNotNull('locked_at')
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->whereHas('job_details.site_details', function ($subQuery) use ($cost_center) {
                        $subQuery->where('cost_center', $cost_center);
                    });
                })
                ->with('job_details')
                ->count();

            $payrollLineItem = PayrollLineItem::query()
                ->whereNot('pay_rate_name', 'Bonus')
                ->where('payroll_week', $row['payroll_week_number'].'-'.$row['year'])
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->whereHas('site_details', function ($subQuery) use ($cost_center) {
                        $subQuery->where('cost_center', $cost_center);
                    });
                })
                ->with('site_details')
                ->get()
                ->sum('total_hours');

            $label[] = 'WK'.$row['payroll_week_number'];
            $shift_data[] = $timesheetEntry;
            $hours_data[] = $payrollLineItem;*/

            $timesheetEntry = Timesheet::query()
                ->whereBetween('date', [$start_date->toDateString(), $end_date->toDateString()])
                ->whereNotNull('locked_at')
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->whereHas('job_details.site_details', function ($subQuery) use ($cost_center) {
                        $subQuery->where('cost_center', $cost_center);
                    });
                })
                ->with('job_details')
                ->get();

            $label[] = 'WK'.$row['payroll_week_number'];
            $shift_data[] = sizeof($timesheetEntry->toArray());
            $hours_data[] = $timesheetEntry->sum('hours_worked');
        }

        return [
            'label' => $label,
            'shift_data' => $shift_data,
            'hours_data' => $hours_data
        ];
    }

    private function workerPlus($cost_center) {
        $startDate = Carbon::today()->subDays(7)->startOfDay();
        $endDate = Carbon::today()->endOfDay();

        //Total active workers
        $totalActiveWorkers = Worker::query()
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('worker_cost_center', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->with('worker_cost_center')
            ->where('status', 'Active')
            ->count();

        //Workers added (last 7 days)
        $total_worker_added = Worker::query()
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('worker_cost_center', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->with('worker_cost_center')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        //Leavers (last 7 days)
        $total_worker_leaver = Worker::query()
            ->when($cost_center != '', function ($query) use ($cost_center) {
                $query->whereHas('worker_cost_center', function ($subQuery) use ($cost_center) {
                    $subQuery->where('cost_center', $cost_center);
                });
            })
            ->whereHas('leaverLog', function ($subQuery) use ($startDate, $endDate) {
                $subQuery->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->where('status', 'Leaver')
            ->with(['worker_cost_center', 'leaverLog'])
            ->count();

        return [
            'total_active_worker' => $totalActiveWorkers,
            'total_worker_added' => $total_worker_added,
            'total_worker_leaver' => $total_worker_leaver,
        ];
    }

    private function topClient($cost_center, $payroll_week) {
        $explode = explode('-', $payroll_week);
        $payroll_week_data = PayrollWeekDate::query()
            ->where('payroll_week_number', $explode[0])
            ->where('year', $explode[1])
            ->first();
        /*$end_date = Carbon::parse($payroll_week_data['pay_date']);
        $start_date = $end_date->copy()->subDay(6);*/
        $start_date = $payroll_week_data['monday_payroll_start'];
        $end_date = $payroll_week_data['monday_payroll_end'];

        //$fiveWeeksAgo = Carbon::now()->subWeeks(5);
        $topClients = DB::table('timesheets as t')
            ->join('client_jobs as cj', 'cj.id', '=', 't.job_id')
            ->join('sites as s', 's.id', '=', 'cj.site_id')
            ->join('clients as c', 'c.id', '=', 'cj.client_id')
            ->select('c.id as client_id', 'c.company_name as client_name', DB::raw('SUM(t.hours_worked) as total_hours'))
            //->where('t.date', '>=', $fiveWeeksAgo)
            ->where('t.date', '>=', $start_date)
            ->where('t.date', '<=', $end_date)
            ->where('t.locked_at', '!=', null)
            ->when($cost_center, function ($query) use ($cost_center) {
                return $query->where('s.cost_center', $cost_center);
            })
            ->groupBy('c.id', 'c.company_name')
            ->orderByDesc('total_hours')
            ->limit(5)
            ->get();

        $totalHours = DB::table('timesheets as t')
            ->join('client_jobs as cj', 'cj.id', '=', 't.job_id')
            ->join('sites as s', 's.id', '=', 'cj.site_id')
            //->where('t.date', '>=', $fiveWeeksAgo)
            ->where('t.date', '>=', $start_date)
            ->where('t.date', '<=', $end_date)
            ->where('t.locked_at', '!=', null)
            ->when($cost_center, function ($query) use ($cost_center) {
                return $query->where('s.cost_center', $cost_center);
            })
            ->sum('t.hours_worked');

        $topClientIds = $topClients->pluck('client_id')->toArray();
        $otherHours = DB::table('timesheets as t')
            ->join('client_jobs as cj', 'cj.id', '=', 't.job_id')
            ->join('sites as s', 's.id', '=', 'cj.site_id')
            ->join('clients as c', 'c.id', '=', 'cj.client_id')
            //->where('t.date', '>=', $fiveWeeksAgo)
            ->where('t.date', '>=', $start_date)
            ->where('t.date', '<=', $end_date)
            ->where('t.locked_at', '!=', null)
            ->whereNotIn('c.id', $topClientIds)
            ->when($cost_center, function ($query) use ($cost_center) {
                return $query->where('s.cost_center', $cost_center);
            })
            ->sum('t.hours_worked');

        $topClients->push((object)[
            'client_id' => 'other',
            'client_name' => 'Other',
            'total_hours' => $otherHours,
        ]);

        $labels = [];
        $data = [];
        foreach ($topClients as $client) {
            $percentage = $totalHours > 0 ? round(($client->total_hours / $totalHours) * 100, 2) : 0;
            $labels[] = "{$client->client_name} {$percentage}%";
            $data[] = $client->total_hours;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getShiftWithSpaceInNextSevenDays(Request $request) {
        try {
            $cost_center = $request->input('cost_center');
            $client_id = $request->input('client_id');

            $shiftWithSpace = JobShift::query()
                //->whereBetween('date', [Carbon::today()->toDateString(), Carbon::today()->addDay($request->input('days'))->toDateString()])
                ->when(request('days') == '1', function ($query) {
                    $query->whereRaw("STR_TO_DATE(CONCAT(`date`, ' ', `start_time`), '%Y-%m-%d %H:%i:%s') > ?", [Carbon::now()->addHour()])
                        ->whereRaw("STR_TO_DATE(CONCAT(`date`, ' ', `start_time`), '%Y-%m-%d %H:%i:%s') < ?", [Carbon::now()->addHours(36)]);
                })
                ->when(request('days') == '7', function ($query) {
                    $query->whereBetween('date', [Carbon::today()->toDateString(), Carbon::today()->addDay(7)->toDateString()]);
                })
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->whereHas('client_job_details.site_details', function ($subQuery) use ($cost_center) {
                        $subQuery->where('cost_center', $cost_center);
                    });
                })
                ->when(isset($client_id), function ($query) use ($client_id) {
                    $query->whereHas('client_job_details', function ($subQuery) use ($client_id) {
                        $subQuery->where('client_id', $client_id);
                    });
                })
                ->with(['JobShiftWorker_details', 'client_job_details'])
                ->get()
                ->map(function ($jobShift) {
                    if (count($jobShift->JobShiftWorker_details) < $jobShift->number_workers) {
                        return $jobShift;
                    }
                    return null;
                })
                ->filter()
                ->toArray();

            $array = [];
            if ($shiftWithSpace) {
                foreach ($shiftWithSpace as $row) {
                    $array[] = [
                        'date' => date('d-m-Y', strtotime($row['date'])),
                        'client' => $row['client_job_details']['client_details']['company_name'],
                        'site' => $row['client_job_details']['site_details']['site_name'],
                        'job' => $row['client_job_details']['name'],
                        'spaces_filled' => count($row['job_shift_worker_details']).'/'.$row['number_workers'],
                        'action' => '<a href="'.url('view-job-shift/'.$row['id']).'">
                                        <i class="fs-2 las la-arrow-right"></i>
                                    </a>',
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($shiftWithSpace),
                'recordsFiltered'   => count($shiftWithSpace),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function newIndex() {
        $date = Carbon::now()->format('Y-m-d');
        $jobShift = JobShift::with('client_job_details.site_details', 'client_job_details.client_details')
            ->select('id', 'job_id', 'date')
            ->where('date', $date)
            ->get();

        $totals = collect([
            'jobs' => 'client_job_details.id',
            'sites' => 'client_job_details.site_details.id',
            'clients' => 'client_job_details.client_details.id'
        ])->mapWithKeys(function ($path, $key) use ($jobShift) {
            return ["totalUnique" . ucfirst($key) => $jobShift->pluck($path)->unique()->count()];
        });

        $todayData = [
            'totalBooking'  => count($jobShift),
            'totalJobs'     => $totals->get('totalUniqueJobs'),
            'totalSites'    => $totals->get('totalUniqueSites'),
            'totalClients'  => $totals->get('totalUniqueClients'),
        ];

        $previousPayrollWeek = PayrollWeekDate::query()
            ->where('pay_date', '<=', Carbon::now()->toDateString())
            ->latest('pay_date')
            ->first();

        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('dashboards/new_dashboard', compact(['todayData', 'previousPayrollWeek', 'costCentre']));
    }

    public function getNewDashboardData(Request $request) {
        try {
            $cost_center = $request->input('cost_center');
            $payroll_week = $request->input('payroll_week');

            $returnArray = [
                'alert_section' => $this->alert($cost_center),
                'booking_section' => $this->bookings($cost_center),
                'week_snapshot_section' => $this->weekSnapshot($cost_center, $payroll_week),
                'top_client_section' => $this->topClient($cost_center, $payroll_week),
                'shift_and_hours_trends' => $this->shift_and_hours_trends($cost_center),
            ];

            return self::responseWithSuccess('Dashboard Data', $returnArray);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function quickDashboardSearchWorkerClientJob(Request $request) {
        try {
            $params = $request->input();

            if (empty($params['keyword']['term'])) {
                return self::responseWithError('Please enter a keyword to search...');
            }

            $searchTerms = explode(' ', $params['keyword']['term']);
            $costCenter = $params['cost_center'] ?? null;

            $workers = Worker::select(['id', 'first_name', 'middle_name', 'last_name', 'date_of_birth'])
                ->with('worker_cost_center')
                ->where(function ($query) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $query->where('first_name', 'LIKE', "%$term%")
                            ->orWhere('middle_name', 'LIKE', "%$term%")
                            ->orWhere('last_name', 'LIKE', "%$term%");
                    }
                })
                ->when($costCenter, function ($query) use ($costCenter) {
                    $query->whereHas('worker_cost_center', function ($subQuery) use ($costCenter) {
                        $subQuery->where('cost_center', $costCenter);
                    });
                })
                ->get()
                ->map(fn($worker) => [
                    'id'   => (string) $worker->id,
                    'text' => "{$worker->first_name} {$worker->middle_name} {$worker->last_name} (".date('d/m/Y', strtotime($worker->date_of_birth)).")",
                    'url' => url('view-worker-details/'.$worker->id)
                ]);

            $clients = Client::where(function ($query) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $query->orWhere('company_name', 'LIKE', "%$term%");
                }
            })
            ->get()
            ->map(fn($client) => [
                'id'   => (string) $client->id,
                'text' => $client->company_name,
                'url' => url('view-client-details/'.$client->id)
            ]);


            $clientJobs = ClientJob::where(function ($query) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $query->orWhere('name', 'LIKE', "%$term%");
                }
            })
            ->when($costCenter, function ($query) {
                $query->whereHas('site_details', function ($subQuery) {
                    $subQuery->where('cost_center', request('cost_center'));
                });
            })
            ->get()
            ->map(fn($clientJob) => [
                'id'   => (string) $clientJob->id,
                'text' => $clientJob->name,
                'url' => url('view-client-job/'.$clientJob->id)
            ]);


            $responseData = [];
            if ($workers->isNotEmpty())   $responseData[] = ['text' => 'Workers', 'children' => $workers];
            if ($clients->isNotEmpty())   $responseData[] = ['text' => 'Clients', 'children' => $clients];
            if ($clientJobs->isNotEmpty()) $responseData[] = ['text' => 'Jobs', 'children' => $clientJobs];

            return self::responseWithSuccess('Search results', $responseData);
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getBookingInvitationToChase(Request $request) {
        $startDate = Carbon::today()->toDateString();
        $endDate = Carbon::today()->addDay(7)->toDateString();
        $client_id = $request->input('client_id');
        $bookingInvitationToChase = JobShiftWorker::query()
            ->whereBetween('invited_at', [$startDate, $endDate])
            ->whereNull('confirmed_at')
            ->whereNull('declined_at')
            ->whereNull('cancelled_at')
            ->when(request('cost_center') != '', function ($query) {
                $query->whereHas('jobShift.client_job_details.site_details', function ($subQuery) {
                    $subQuery->where('cost_center', request('cost_center'));
                });
            })
            ->when(isset($client_id), function ($query) use ($client_id) {
                $query->whereHas('jobShift.client_job_details', function ($subQuery) use ($client_id) {
                    $subQuery->where('client_id', $client_id);
                });
            })
            ->with(['jobShift','worker'])
            ->get();

        $array = [];
        if ($bookingInvitationToChase) {
            foreach ($bookingInvitationToChase as $row) {
                $array[] = [
                    'date' => date('d-m-Y', strtotime($row['jobShift']['date'])),
                    'job' => ($row['jobShift']) ? $row['jobShift']['client_job_details']['name'] : '',
                    'cost_center' => $row['jobShift']['client_job_details']['site_details']['cost_center'],
                    'worker' => '<a href="'.url('view-worker-details/'.$row['worker_id']).'">'.$row['worker']['first_name'].' '.$row['worker']['middle_name'].' '.$row['worker']['last_name'].'</a>',
                    'additional_info' => $this->additional_info(
                        $row['worker']['email_address'],
                        $row['worker']['mobile_number'],
                        $row['jobShift']['client_job_details']['site_details']['site_name'],
                        $row['jobShift']['client_job_details']['client_details']['company_name']
                    ),
                    'action' => '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" data-id="'.$row['id'].'" id="confirm_booking_worker"><i class="fs-2 las la-check"></i></a>
                        <a href="'.url('view-job-shift/'.$row['job_shift_id']).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm"><i class="fs-2 las la-calendar-day"></i></a>',
                ];
            }
        }
        return [
            'draw'              => 1,
            'recordsTotal'      => count($bookingInvitationToChase),
            'recordsFiltered'   => count($bookingInvitationToChase),
            'data'              => $array
        ];
    }

    private function additional_info($email_address, $mobile_number, $site_name, $company_name) {
        return '<a href="javascript:;" title="'.$email_address.'"><i class="fs-2 las la-envelope-open"></i></a>
            <a href="javascript:;" title="'.$mobile_number.'"><i class="fs-2 las la-mobile-alt"></i></a>
            <a href="javascript:;" title="'.$site_name.'"><i class="fs-2 las la-map-marker"></i></a>
            <a href="javascript:;" title="'.$company_name.'"><i class="fs-2 las la-industry"></i></a>';
    }

    public function getTotalBookings(Request $request) {
        try {
            $cost_center    = $request->input('cost_center');
            $bar_index      = $request->input('booking_bar_index');

            $jobShiftWorker = JobShiftWorker::query()
                ->when($bar_index != 'All', function ($query) use ($bar_index) {
                    $query->whereDate('shift_date', Carbon::today()->addDay($bar_index)->toDateString());
                })
                ->when($bar_index == 'All', function ($query) {
                    $query->whereBetween('shift_date', [Carbon::today()->toDateString(), Carbon::today()->addDay(7)->toDateString()]);
                })
                ->whereNotNull('confirmed_at')
                ->whereNull('cancelled_at')
                ->whereNull('declined_at')
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->whereHas('jobShift.client_job_details.site_details', function ($subQuery) use ($cost_center) {
                        $subQuery->where('cost_center', $cost_center);
                    });
                })
                ->with(['jobShift', 'worker'])
                ->get();

            $array = [];
            if ($jobShiftWorker) {
                foreach ($jobShiftWorker as $row) {
                    $array[] = [
                        'date' => date('d-m-Y', strtotime($row['shift_date'])),
                        'worker' => '<a href="'.url('view-worker-details/'.$row['worker']['id']).'">'.$row['worker']['first_name'].' '.$row['worker']['middle_name'].' '.$row['worker']['last_name'].'</a>',
                        'job' => $row['jobShift']['client_job_details']['name'],
                        'site' => $row['jobShift']['client_job_details']['site_details']['site_name'],
                        'client' => $row['jobShift']['client_job_details']['client_details']['company_name'],
                        'cost_center' => $row['jobShift']['client_job_details']['site_details']['cost_center'],
                        'action' => '<a href="'.url('view-job-shift/'.$row['job_shift_id']).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1"><i class="fs-2 las la-calendar-day"></i></a>',
                    ];
                }
            }

            return [
                'draw'              => 1,
                'recordsTotal'      => count($array),
                'recordsFiltered'   => count($array),
                'data'              => $array
            ];

        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getTotalJobShift(Request $request) {
        try {
            $cost_center    = $request->input('cost_center');
            $bar_index      = $request->input('job_shift_bar_index');

            $jobShiftWorker = JobShiftWorker::query()
                ->when($bar_index != 'All', function ($query) use ($bar_index) {
                    $query->whereDate('shift_date', Carbon::today()->addDay($bar_index)->toDateString());
                })
                ->when($bar_index == 'All', function ($query) {
                    $query->whereBetween('shift_date', [Carbon::today()->toDateString(), Carbon::today()->addDay(7)->toDateString()]);
                })
                ->whereNotNull('confirmed_at')
                ->whereNull('cancelled_at')
                ->whereNull('declined_at')
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->whereHas('jobShift.client_job_details.site_details', function ($subQuery) use ($cost_center) {
                        $subQuery->where('cost_center', $cost_center);
                    });
                })
                ->with(['jobShift', 'worker'])
                ->get()
                ->toArray();

            $jobShift = JobShift::query()
                ->whereIn('id', array_column($jobShiftWorker, 'job_shift_id'))
                ->with('client_job_details')
                ->get();

            $array = [];
            if ($jobShift) {
                foreach ($jobShift as $row) {
                    $array[] = [
                        'date' => date('d-m-Y', strtotime($row['date'])),
                        'job' => $row['client_job_details']['name'],
                        'site' => $row['client_job_details']['site_details']['site_name'],
                        'client' => $row['client_job_details']['client_details']['company_name'],
                        'cost_center' => $row['client_job_details']['site_details']['cost_center'],
                        'action' => '<a href="'.url('view-job-shift/'.$row['id']).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1"><i class="fs-2 las la-calendar-day"></i></a>',
                    ];
                }
            }

            return [
                'draw'              => 1,
                'recordsTotal'      => count($array),
                'recordsFiltered'   => count($array),
                'data'              => $array
            ];

        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getTotalJob(Request $request) {
        try {
            $cost_center    = $request->input('cost_center');
            $bar_index      = $request->input('job_bar_index');

            $jobShiftWorker = JobShiftWorker::query()
                ->when($bar_index != 'All', function ($query) use ($bar_index) {
                    $query->whereDate('shift_date', Carbon::today()->addDay($bar_index)->toDateString());
                })
                ->when($bar_index == 'All', function ($query) {
                    $query->whereBetween('shift_date', [Carbon::today()->toDateString(), Carbon::today()->addDay(7)->toDateString()]);
                })
                ->whereNotNull('confirmed_at')
                ->whereNull('cancelled_at')
                ->whereNull('declined_at')
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->whereHas('jobShift.client_job_details.site_details', function ($subQuery) use ($cost_center) {
                        $subQuery->where('cost_center', $cost_center);
                    });
                })
                ->with(['jobShift', 'worker'])
                ->get()
                ->toArray();

            $job = ClientJob::query()
                ->whereIn('id', array_column(array_column($jobShiftWorker, 'job_shift'), 'job_id'))
                ->with(['client_details', 'site_details'])
                ->get();

            $array = [];
            if ($job) {
                foreach ($job as $row) {
                    $array[] = [
                        'job' => $row['name'],
                        'site' => $row['site_details']['site_name'],
                        'client' => $row['client_details']['company_name'],
                        'action' => '<a href="'.url('view-client-job/'.$row['id']).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1"><i class="fs-2 las la-arrow-right"></i></a>',
                    ];
                }
            }

            return [
                'draw'              => 1,
                'recordsTotal'      => count($array),
                'recordsFiltered'   => count($array),
                'data'              => $array
            ];

        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getTotalSite(Request $request) {
        try {
            $cost_center    = $request->input('cost_center');
            $bar_index      = $request->input('site_bar_index');

            $jobShiftWorker = JobShiftWorker::query()
                ->when($bar_index != 'All', function ($query) use ($bar_index) {
                    $query->whereDate('shift_date', Carbon::today()->addDay($bar_index)->toDateString());
                })
                ->when($bar_index == 'All', function ($query) {
                    $query->whereBetween('shift_date', [Carbon::today()->toDateString(), Carbon::today()->addDay(7)->toDateString()]);
                })
                ->whereNotNull('confirmed_at')
                ->whereNull('cancelled_at')
                ->whereNull('declined_at')
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->whereHas('jobShift.client_job_details.site_details', function ($subQuery) use ($cost_center) {
                        $subQuery->where('cost_center', $cost_center);
                    });
                })
                ->with(['jobShift', 'worker'])
                ->get()
                ->toArray();

            $site = Site::query()
                ->whereIn('id', array_column(array_column(array_column($jobShiftWorker, 'job_shift'), 'client_job_details'), 'site_id'))
                ->with('client_details')
                ->get();

            $array = [];
            if ($site) {
                foreach ($site as $row) {
                    $array[] = [
                        'site' => $row['site_name'],
                        'client' => $row['client_details']['company_name'],
                        'action' => '<a href="'.url('view-site/'.$row['id']).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1"><i class="fs-2 las la-arrow-right"></i></a>',
                    ];
                }
            }

            return [
                'draw'              => 1,
                'recordsTotal'      => count($array),
                'recordsFiltered'   => count($array),
                'data'              => $array
            ];

        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getTotalClient(Request $request) {
        try {
            $cost_center    = $request->input('cost_center');
            $bar_index      = $request->input('client_bar_index');

            $jobShiftWorker = JobShiftWorker::query()
                ->when($bar_index != 'All', function ($query) use ($bar_index) {
                    $query->whereDate('shift_date', Carbon::today()->addDay($bar_index)->toDateString());
                })
                ->when($bar_index == 'All', function ($query) {
                    $query->whereBetween('shift_date', [Carbon::today()->toDateString(), Carbon::today()->addDay(7)->toDateString()]);
                })
                ->whereNotNull('confirmed_at')
                ->whereNull('cancelled_at')
                ->whereNull('declined_at')
                ->when($cost_center != '', function ($query) use ($cost_center) {
                    $query->whereHas('jobShift.client_job_details.site_details', function ($subQuery) use ($cost_center) {
                        $subQuery->where('cost_center', $cost_center);
                    });
                })
                ->with(['jobShift', 'worker'])
                ->get()
                ->toArray();

            $client = Client::query()
                ->whereIn('id', array_column(array_column(array_column($jobShiftWorker, 'job_shift'), 'client_job_details'), 'client_id'))
                ->get();

            $array = [];
            if ($client) {
                foreach ($client as $row) {
                    $array[] = [
                        'client' => $row['company_name'],
                        'action' => '<a href="'.url('view-client-details/'.$row['id']).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1"><i class="fs-2 las la-arrow-right"></i></a>',
                    ];
                }
            }

            return [
                'draw'              => 1,
                'recordsTotal'      => count($array),
                'recordsFiltered'   => count($array),
                'data'              => $array
            ];

        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    /*public function insertPayrollWeek() {
        $year = '2025';
        $lastDayOfYear = Carbon::create($year, 12, 31);
        $totalWeeks = $lastDayOfYear->isoWeek() == 1 ? 52 : 53;

        $weekNumber = 1;
        $startDate = Carbon::create($year, 1, 1)->startOfWeek(Carbon::SUNDAY);

        $weeksData = [];
        for ($i = 0; $i <= $totalWeeks; $i++) {

            $tuesday_payroll_start = $startDate->copy()->subDays(12);
            $wednesday_payroll_start = $tuesday_payroll_start->copy()->addDay();
            $thursday_payroll_start = $wednesday_payroll_start->copy()->addDay();
            $friday_payroll_start = $thursday_payroll_start->copy()->addDay();
            $saturday_payroll_start = $friday_payroll_start->copy()->addDay();
            $sunday_payroll_start = $saturday_payroll_start->copy()->addDay();
            $monday_payroll_start = $sunday_payroll_start->copy()->addDay();

            $weekData = [
                'payroll_week_number' => $weekNumber,
                'year' => $year,
                'pay_date' => $startDate->copy()->addDays(5)->format('Y-m-d'),

                'tuesday_payroll_start' => $tuesday_payroll_start->format('Y-m-d'),
                'tuesday_payroll_end' => $tuesday_payroll_start->copy()->addDays(6)->format('Y-m-d'),

                'wednesday_payroll_start' => $wednesday_payroll_start->format('Y-m-d'),
                'wednesday_payroll_end' => $wednesday_payroll_start->copy()->addDays(6)->format('Y-m-d'),

                'thursday_payroll_start' => $thursday_payroll_start->format('Y-m-d'),
                'thursday_payroll_end' => $thursday_payroll_start->copy()->addDays(6)->format('Y-m-d'),

                'friday_payroll_start' => $friday_payroll_start->format('Y-m-d'),
                'friday_payroll_end' => $friday_payroll_start->copy()->addDays(6)->format('Y-m-d'),

                'saturday_payroll_start' => $saturday_payroll_start->format('Y-m-d'),
                'saturday_payroll_end' => $saturday_payroll_start->copy()->addDays(6)->format('Y-m-d'),

                'sunday_payroll_start' => $sunday_payroll_start->format('Y-m-d'),
                'sunday_payroll_end' => $sunday_payroll_start->copy()->addDays(6)->format('Y-m-d'),

                'monday_payroll_start' => $monday_payroll_start->format('Y-m-d'),
                'monday_payroll_end' => $monday_payroll_start->copy()->addDays(6)->format('Y-m-d'),
            ];

            $weeksData[] = $weekData;

            $startDate->addWeek();
            $weekNumber++;
        }
        PayrollWeekDate::query()->insert($weeksData);
    }*/
}
