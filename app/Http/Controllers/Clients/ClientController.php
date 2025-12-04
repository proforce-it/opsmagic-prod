<?php

namespace App\Http\Controllers\Clients;

use App\Helper\Activity\ActivityLogs;
use App\Helper\Clients\ClientHelper;
use App\Helper\File\FileHelper;
use App\Helper\Job\JobHelper;
use App\Helper\Workers\RightToWorkHelper;
use App\Helper\Workers\WorkerHelper;
use App\Http\Controllers\Controller;
use App\Models\Client\Client;
use App\Models\Client\ClientContact;
use App\Models\Client\ClientDocument;
use App\Models\Client\ClientJob;
use App\Models\Client\ClientJobWorker;
use App\Models\Client\Site;
use App\Models\Client\SiteWeekLock;
use App\Models\Group\CostCentre;
use App\Models\Group\Group;
use App\Models\Group\GroupWithJob;
use App\Models\Group\GroupWithWorker;
use App\Models\Job\ClientJobPayRate;
use App\Models\Job\JobLine;
use App\Models\Job\JobShift;
use App\Models\Job\JobShiftWorker;
use App\Models\Job\PayrollLineItem;
use App\Models\Location\Country;
use App\Models\Note\Note;
use App\Models\Payroll\PayrollWeekDate;
use App\Models\PickUpPoint\PickUpPoint;
use App\Models\Timesheet\Timesheet;
use App\Models\Worker\Absence;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

//use function Symfony\Component\HttpKernel\DataCollector\getMessage;

class ClientController extends Controller
{
    use JsonResponse;
    public function clientManagement() {
        return view('clients.dis_client');

    }

    public function listOfClient(Request $request) {
        try {

            $status = $request->input('status');
            if (in_array($status, ['Active', 'Prospect'])) {
                $clients = Client::query()->where('status', $status)->with(['client_documents', 'client_site_details'])->get();
            } elseif ($status === 'Archived') {
                $clients = Client::onlyTrashed()->with(['client_documents', 'client_site_details'])->get();
            } elseif ($status === 'All') {
                $clients = Client::withTrashed()->with(['client_documents', 'client_site_details'])->get();
            } else {
                $clients = [];
            }

            $array  = [];

            if ($clients) {
                foreach ($clients as $row) {
                    $site = Site::query()->where('client_id', $row['id'])->get();
                    $noOfSites = $site->count();

                    $logo = ($row['company_logo'])
                        ? '<a href="'.url('view-client-details/'.$row['id']).'"><img src="'.asset('workers/client_document/'.$row['company_logo']).'" style="object-fit: contain; object-position: left 50%; width: 150px; height: 50px; display: block;"></a>'
                        : '<a href="'.url('view-client-details/'.$row['id']).'"><i class="fs-xxl-2hx las la-industry bg-gray-200 rounded-3 p-2"></i></a>';

                    $array[] = [
                        'company_logo'      => $logo,
                        'company_name'      => $row['company_name'],
                        'status'            => (!$row['deleted_at']) ? $row['status'] : 'Archived',
                        'no_of_sites'       => $noOfSites,
                        'flags'             => ClientHelper::getFlags($row->toArray()),
                        'actions'           => $this->action($row['id'], $row['deleted_at']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($clients),
                'recordsFiltered'   => count($clients),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function action($id, $deleted_at) {

        $action = '';
        if (!$deleted_at) {
            $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" data-kt-quotation-table-filter="delete_row" id="delete_customer" data-client-id="'.$id.'">
                        <i class="fs-2 las la-archive"></i>
                        </a>';
        }
        $action .= '<a href="'.url('view-client-details/'.$id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_client" data-client-id="'.$id.'">
                      <i class="fs-2 las la-arrow-right"></i>
                </a>';

        return $action;
    }

    public function createClient() {
        $country = Country::query()->select(['id', 'name'])->get();
        return view('clients.add_client', compact('country'));
    }

    public function storeClient(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->input(), [
                'company_name' => 'required|unique:clients,company_name',
                'company_registration_number' => 'required|unique:clients,company_number',
                'vat_number' => 'nullable|unique:clients,vat_number',
                'first_name' => 'required',
                'last_name' => 'required',
                'telephone_one' => 'required',
                'email_address' => 'required',
                'address_type' => 'required',
                'address_line_one' => 'required',
                'city' => 'required',
                'country' => 'required',
                'sector' => 'required'
            ], [
                'company_name.unique' => 'A client matching these details already exists',
                'company_number.unique' => 'A client matching these details already exists',
                'vat_number.unique' => 'A client matching these details already exists',
            ]);

            $message = $validator->errors()->messages();
            if ($message)
                return self::validationError($message);

            $params = $request->input();
            $client = Client::query()->create([
                'company_name' => $params['company_name'],
                'company_number' => $params['company_registration_number'],
                'vat_number' => $params['vat_number'],
                'sector' => $params['sector'],
                'address_type' => $params['address_type'],
                'address_line_one' => $params['address_line_one'],
                'address_line_two' => $params['address_line_two'],
                'city_town' => $params['city'],
                'county' => $params['country'],
                'postcode' => $params['postcode'],
                //  'country_option' => $params['country_option'],
                'website' => $params['website']
            ]);

            /*--- BEGIN CLIENT SITE & CLIENT CONTACT SECTION ---*/
            if ($params['address_type'] == 'Site address') {
                $site = Site::query()->create([
                    'client_id' => $client['id'],
                    'site_name' => $params['company_name'],
                    'address_line_1' => $params['address_line_one'],
                    'address_line_2' => $params['address_line_two'],
                    'city' => $params['city'],
                    'country' => $params['country'],
                    'postcode' => $params['postcode'],
                    'latitude' => null,
                    'longitude' => null,
                ]);
            }

            ClientContact::query()->create([
                'client_id' => $client['id'],
                'first_name' => $params['first_name'],
                'last_name' => $params['last_name'],
                'email' => $params['email_address'],
                'primary_contact_number' => $params['telephone_one'],
                'secondary_contact_number' => $params['telephone_two'],
                'site_id' => (isset($site) && $site['id']) ? $site['id'] : '',
                'primary_contact' => 1,
            ]);
            /*--- END CLIENT SITE & CLIENT CONTACT SECTION ---*/
            ActivityLogs::createAndDeleteLog($client['id'], 'Create', 'Client create', 'Client');

            DB::commit();
            return self::responseWithSuccess('Client details successfully saved.', [
                'client_id' => $client['id'],
                'client_name' => $client['company_name'],
            ]);
        } catch (\Exception $e) {

            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function viewClient($id) {
        $client = Client::withTrashed()->where('id', $id)->with(['client_documents', 'client_site_details'])->first()->toArray();
        $previousPayrollWeek = PayrollWeekDate::query()
            ->where('pay_date', '<=', \Illuminate\Support\Carbon::now()->toDateString())
            ->latest('pay_date')
            ->first();
        $primaryContact = ClientContact::query()->where('client_id', $id)->where('archived',0)->get();
        $primaryContactData = $primaryContact->firstWhere('primary_contact', 1);
        $flags = ClientHelper::getFlags($client);
        $country = Country::query()->select(['id', 'name'])->get();
        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('clients.view_client', compact('client',  'previousPayrollWeek','primaryContact', 'primaryContactData', 'flags', 'country', 'costCentre'));
    }

    public function getDashboardData(Request $request) {
        try {
            $client_id = $request->input('client_id');
            $returnArray = [
                'alert_section' => $this->alert($client_id),
                'snapshot_section' => $this->weekSnapshot($client_id, $request->input('payroll_week')),
                'booking_section' => $this->bookings($client_id),
                'top_job_section' => $this->topJobs($client_id),
                'shift_and_hours_trends' => $this->shift_and_hours_trends($client_id),
            ];

            return self::responseWithSuccess('Dashboard Data', $returnArray);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function alert($client_id) {
        $startDate = Carbon::today()->toDateString();

        //Shifts with spaces in tomorrow
        $shiftSpaceTomorrow = Carbon::today()->addDay(1)->toDateString();
        $shiftWithSpaceTomorrow = JobShift::query()
            ->whereBetween('date', [$startDate, $shiftSpaceTomorrow])
            ->with(['JobShiftWorker_details', 'client_job_details'])
            ->get()
            ->map(function ($jobShift) use ($client_id) {
                if ($jobShift->client_job_details->client_id == $client_id && count($jobShift->JobShiftWorker_details) < $jobShift->number_workers) {
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
            ->with(['JobShiftWorker_details', 'client_job_details'])
            ->get()
            ->map(function ($jobShift) use ($client_id) {
                if ($jobShift->client_job_details->client_id == $client_id && count($jobShift->JobShiftWorker_details) < $jobShift->number_workers) {
                    return $jobShift;
                }
                return null;
            })
            ->filter()
            ->count();

        //BOOKING INVITATIONS TO CHASE (<7 DAYS)
        $bookingInvitationStartDate = Carbon::today()->subDay(7)->toDateString();
        $bookingInvitationChase = JobShiftWorker::query()
            ->whereBetween('invited_at', [$bookingInvitationStartDate, $startDate])
            ->whereNull('confirmed_at')
            ->whereNull('declined_at')
            ->whereNull('cancelled_at')
            ->with('jobShift')
            ->get()
            ->filter(function ($jobShiftWorker) use ($client_id) {
                return $jobShiftWorker->jobShift->client_job_details->client_id == $client_id;
            })
            ->count();

        return [
            'shift_with_space_tomorrow' => $shiftWithSpaceTomorrow,
            'shift_with_space' => $shiftWithSpace,
            'booking_invitation_chase' => $bookingInvitationChase
        ];
    }

    private function weekSnapshot($client_id, $payroll_week) {
        $explode = explode('-', $payroll_week);
        $pw_payroll_week = ($explode[0] != 1) ? $explode[0] - 1 : $explode[0];
        $pw_payroll_week = $pw_payroll_week.'-'.$explode[1];

        /*--- NEXT 7 DAYS TIMESHEET ENTRIES ---*/
        $payroll_week_data = PayrollWeekDate::query()
            ->where('payroll_week_number', $explode[0])
            ->where('year', $explode[1])
            ->first();

        $start_date = Carbon::parse($payroll_week_data['monday_payroll_start']);
        $end_date = Carbon::parse($payroll_week_data['monday_payroll_end']);

        $timesheetQuery = Timesheet::query()
            ->whereBetween('date', [$start_date->toDateString(), $end_date->toDateString()])
            ->whereNotNull('locked_at')
            ->when($client_id != '', function ($query) use ($client_id) {
                $query->whereHas('job_details', function ($subQuery) use ($client_id) {
                    $subQuery->where('client_id', $client_id);
                });
            });
        $timesheetEntry = $timesheetQuery->with('job_details')->count();

        /*--- LAST 7 DAYS TIMESHEET ENTRIES ---*/
        $pw_start_date = $start_date->copy()->subDay(6);
        $pw_timesheetEntry = Timesheet::query()
            ->whereBetween('date', [$pw_start_date->toDateString(), $start_date->toDateString()])
            ->whereNotNull('locked_at')
            ->when($client_id != '', function ($query) use ($client_id) {
                $query->whereHas('job_details', function ($subQuery) use ($client_id) {
                    $subQuery->where('client_id', $client_id);
                });
            })
            ->with('job_details')
            ->count();

        /*--- BEGIN CURRENT WEEK DATA ---*/
        $payrollLineItem = PayrollLineItem::query()
            ->whereNot('pay_rate_name', 'Bonus')
            ->where('payroll_week', $payroll_week)
            ->when($client_id != '', function ($query) use ($client_id) {
                $query->whereHas('job_details', function ($subQuery) use ($client_id) {
                    $subQuery->where('client_id', $client_id);
                });
            })
            ->with('job_details')
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
        /*--- END CURRENT WEEK DATA ---*/

        /*--- BEGIN PREVIOUS WEEK DATA ---*/
        $pw_payrollLineItem = PayrollLineItem::query()
            ->whereNot('pay_rate_name', 'Bonus')
            ->where('payroll_week', $pw_payroll_week)
            ->when($client_id != '', function ($query) use ($client_id) {
                $query->whereHas('job_details', function ($subQuery) use ($client_id) {
                    $subQuery->where('client_id', $client_id);
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
        ];
    }

    private function bookings($client_id) {
        $startDate = Carbon::today()->toDateString();
        $endDate = Carbon::today()->addDay(7)->toDateString();
        $preStartDate = Carbon::today()->subDays(7)->toDateString();

        //Chart
        $jobShiftWorkerChart = [];
        for ($date = Carbon::parse($startDate); $date->lte(Carbon::parse($endDate)); $date->addDay()) {
            $filter_date = $date->toDateString();

            $jobShiftWorkerLabel[] = $date->format('d-M');
            $jobShiftWorkerChart[] = JobShiftWorker::query()
                ->whereDate('shift_date', $filter_date)
                ->whereNull('cancelled_at')
                ->whereNull('declined_at')
                ->when($client_id != '', function ($query) use ($client_id) {
                    $query->whereHas('jobShift.client_job_details', function ($subQuery) use ($client_id) {
                        $subQuery->where('client_id', $client_id);
                    });
                })
                ->with('jobShift')
                ->count();
        }

        //Next 7 Days Shift / Job / Site / Client Data
        $jobShiftWorker = array_sum($jobShiftWorkerChart);

        $shifts = JobShift::query()
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNull('cancelled_at')
            ->when($client_id != '', function ($query) use ($client_id) {
                $query->whereHas('client_job_details', function ($subQuery) use ($client_id) {
                    $subQuery->where('client_id', $client_id);
                });
            })
            ->with('client_job_details')
            ->count();

        $job = ClientJob::query()
            ->where('client_id', $client_id)
            ->whereHas('job_shift_details', function ($query) use ($startDate, $endDate)  {
                $query->whereBetween('date', [$startDate, $endDate])
                    ->whereNull('cancelled_at');
            })
            ->with(['site_details', 'job_shift_details'])
            ->count();

        $site = Site::query()
            ->where('client_id', $client_id)
            ->whereHas('job_details.job_shift_details', function ($query) use ($startDate, $endDate)  {
                $query->whereBetween('date', [$startDate, $endDate])
                    ->whereNull('cancelled_at');
            })
            ->with(['job_details'])
            ->count();

        //Previous 7 Days Shift / Job / Site / Client Data
        $preJobShiftWorker = JobShiftWorker::query()
            ->whereBetween('shift_date', [$preStartDate, $startDate])
            ->whereNull('cancelled_at')
            ->whereNull('declined_at')
            ->when($client_id != '', function ($query) use ($client_id) {
                $query->whereHas('jobShift.client_job_details', function ($subQuery) use ($client_id) {
                    $subQuery->where('client_id', $client_id);
                });
            })
            ->with('jobShift')
            ->count();

        $preShift = JobShift::query()
            ->whereBetween('date', [$preStartDate, $startDate])
            ->whereNull('cancelled_at')
            ->when($client_id != '', function ($query) use ($client_id) {
                $query->whereHas('client_job_details', function ($subQuery) use ($client_id) {
                    $subQuery->where('client_id', $client_id);
                });
            })
            ->with('client_job_details')
            ->count();

        $preJob = ClientJob::query()
            ->where('client_id', $client_id)
            ->whereHas('job_shift_details', function ($query) use ($preStartDate, $startDate)  {
                $query->whereBetween('date', [$preStartDate, $startDate])
                    ->whereNull('cancelled_at');
            })
            ->with(['site_details', 'job_shift_details'])
            ->count();

        $preSite = Site::query()
            ->where('client_id', $client_id)
            ->whereHas('job_details.job_shift_details', function ($query) use ($preStartDate, $startDate)  {
                $query->whereBetween('date', [$preStartDate, $startDate])
                    ->whereNull('cancelled_at');
            })
            ->with(['job_details'])
            ->count();

        return [
            'job_shift_worker' => number_format($jobShiftWorker),
            'shifts' => number_format($shifts),
            'job' => number_format($job),
            'site' => number_format($site),

            'job_shift_worker_difference' => WorkerHelper::preparedBookingDifference($jobShiftWorker, $preJobShiftWorker),
            'shift_difference' => WorkerHelper::preparedBookingDifference($shifts, $preShift),
            'job_difference' => WorkerHelper::preparedBookingDifference($job, $preJob),
            'site_difference' => WorkerHelper::preparedBookingDifference($site, $preSite),

            'job_shift_worker_chart' => [
                'labels' => $jobShiftWorkerLabel,
                'data' => $jobShiftWorkerChart,
            ],
        ];
    }

    private function topJobs($client_id) {
        $fiveWeeksAgo = Carbon::now()->subWeeks(5);
        $topJobs = DB::table('timesheets as t')
            ->join('client_jobs as cj', 'cj.id', '=', 't.job_id')
            ->join('clients as c', 'c.id', '=', 'cj.client_id')
            ->select('cj.id as job_id', 'cj.name', DB::raw('SUM(t.hours_worked) as total_hours'))
            ->where('t.date', '>=', $fiveWeeksAgo)
            ->where('cj.client_id', $client_id)
            ->groupBy('cj.id', 'cj.name')
            ->orderByDesc('total_hours')
            ->limit(5)
            ->get();

        $totalHours = DB::table('timesheets as t')
            ->join('client_jobs as cj', 'cj.id', '=', 't.job_id')
            ->where('t.date', '>=', $fiveWeeksAgo)
            ->where('cj.client_id', $client_id)
            ->sum('t.hours_worked');

        $topJobIds = $topJobs->pluck('job_id')->toArray();
        $otherHours = DB::table('timesheets as t')
            ->join('client_jobs as cj', 'cj.id', '=', 't.job_id')
            ->where('t.date', '>=', $fiveWeeksAgo)
            ->where('cj.client_id', $client_id)
            ->whereNotIn('cj.id', $topJobIds)
            ->sum('t.hours_worked');

        $topJobs->push((object)[
            'job_id' => 'other',
            'name' => 'Other',
            'total_hours' => $otherHours,
        ]);

        $labels = [];
        $data = [];
        foreach ($topJobs as $job) {
            $percentage = $totalHours > 0 ? round(($job->total_hours / $totalHours) * 100, 2) : 0;
            $labels[] = "{$job->name} {$percentage}%";
            $data[] = $job->total_hours;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function shift_and_hours_trends($client_id) {
        $currentPayrollWeek = PayrollWeekDate::query()->select('payroll_week_number')
            ->where('pay_date', '<=', Carbon::now()->toDateString())
            ->latest('pay_date')
            ->first();
        $currentWeekNumber = $currentPayrollWeek->payroll_week_number;
        $previousWeeks = range($currentWeekNumber - 4, $currentWeekNumber);

        $payrollWeek = PayrollWeekDate::query()
            ->whereIn('payroll_week_number', $previousWeeks)
            ->where('year', date('Y'))
            ->get();

        $label = [];
        $shift_data = [];
        $hours_data = [];
        foreach ($payrollWeek as $row) {

            $start_date = Carbon::parse($row['pay_date']);
            $end_date = $start_date->copy()->addDays(7);

            $timesheetEntry = Timesheet::query()
                ->whereBetween('date', [$start_date->toDateString(), $end_date->toDateString()])
                ->when($client_id != '', function ($query) use ($client_id) {
                    $query->whereHas('job_details', function ($subQuery) use ($client_id) {
                        $subQuery->where('client_id', $client_id);
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

    public function updateClientBasicDetails(Request $request) {
        try {
            $params = $request->input();

            $validator = Validator::make($request->input(), [
                'company_name' => 'required|unique:clients,company_name,'.$params['basic_details_update_id'],
                'company_registration_number' => 'required|unique:clients,company_number,'.$params['basic_details_update_id'],
                'vat_number' => 'nullable|unique:clients,vat_number,'.$params['basic_details_update_id'],
                'address_line_one' => 'required',
                'city' => 'required',
                'country' => 'required',
                'address_type' => 'required',
                'sector' => 'required'
            ]);

            $message = $validator->errors()->messages();
            if($message) {
                return self::validationError($message);
            }

            $client = Client::query()->where('id', $params['basic_details_update_id'])->first();
            if (!$client) {
                return self::responseWithError('Client details not found please try again.');
            }

            $array = [
                'company_name' => $params['company_name'],
                'company_number' => $params['company_registration_number'],
                'vat_number' => $params['vat_number'],
                'address_line_one' => $params['address_line_one'],
                'address_line_two' => $params['address_line_two'],
                'city_town' => $params['city'],
                'county' => $params['country'],
                'postcode' => $params['postcode'],
                'address_type' => $params['address_type'],
                'website' => $params['website'],
                'sector'  => $params['sector']
            ];

            ActivityLogs::updatesLog($params['basic_details_update_id'],
                'Client update',
                $array,
                Client::query()->select(['company_name', 'company_number', 'vat_number', 'address_line_one', 'address_line_two', 'city_town', 'county', 'postcode', 'address_type', 'website','sector'])->where('id', $params['basic_details_update_id'])->first()->toArray(), 'Client');

            Client::query()->where('id', $params['basic_details_update_id'])->update($array);
            return self::responseWithSuccess('Client basic info successfully updated.');

        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getClientSite(Request $request) {
        try {
            $client_id = $request->input('client_id');
            $site = Site::query()->where('client_id', $client_id)
                ->when(request('status') != 'All', function ($q) {
                    return $q->where('archived', request('status'));
                })
                ->get();

            $array  = [];
            if ($site) {
                foreach ($site as $row) {
                    $array[] = [
                        'site_name' => $row['site_name'],
                        'location'  => $row['address_line_1'].', '.$row['city'].', '.$row['country'], /*$row['postcode']*/
                        'status' => ($row['archived'] == 0) ? 'Active' : 'Archived',
                        'action'   => $this->site_action($row['id'], $row['archived']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($site),
                'recordsFiltered'   => count($site),
                'data'              => $array,
                'display_alert'     => (Site::query()->where('client_id', $client_id)->first()) ? 1 : 0,
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function site_action($id,$archived) {
        $action = '';
        if($archived == 0) {

            $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" data-kt-quotation-table-filter="delete_row" id="delete_client_site" data-site_id="'.$id.'">
                   <i class="fs-2 las la-archive"></i>
                </a>';
        }
            $action .=   '<a href="'.url('view-site/'.$id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_client_site" data-site_id="'.$id.'">
                   <i class="fs-2 las la-arrow-right"></i>
                </a>';

        return $action;
    }

    public function siteDetails($id) {
        $site = Site::query()->where('id', $id)->with('client_details')->first();
        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('clients.view_site', compact('site', 'costCentre'));
    }

    public function storeClientSiteDetails(Request $request) {
        try {
            $validator = Validator::make($request->input(), [
                'site_name' => 'required',
                'site_address_line_one' => 'required',
                'site_city' => 'required',
                'site_country' => 'required',
                //'cost_center' => 'required',
                //'what_three_words_locator' => 'required',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();
            $array = [
                'client_id'         => $params['site_client_id'],
                'site_name'         => $params['site_name'],
                'site_description'  => $params['site_description'],
                'cost_center'       => $params['cost_center'],
                'site_telephone'    => $params['site_telephone'],
                'what_three_words_address' => $params['what_three_words_locator'],
                'address_line_1'    => $params['site_address_line_one'],
                'address_line_2'    => $params['site_address_line_two'],
                'city'              => $params['site_city'],
                'country'           => $params['site_country'],
                'postcode'          => $params['site_postcode'],
                'latitude'          => 0, //$params['site_latitude'],
                'longitude'         => 0, //$params['site_longitude'],
                'site_address_latitude' => $params['site_address_latitude'],
                'site_address_longitude' => $params['site_address_longitude']
            ];

            if ($params['site_id'] == 0) {
                Site::query()->create($array);
                $message = 'Client site details successfully stored.';
            } else {
                $site = Site::query()->where('id', $params['site_id'])->first();
                if (!$site)
                    return self::responseWithError('Site details not found, please try again later.');

                Site::query()->where('id', $params['site_id'])->update($array);
                $message = 'Client site details successfully updated.';
            }

            ClientHelper::automaticClientActive($params['site_client_id']);
            return self::responseWithSuccess($message);
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteClientSiteAction($id) {
        try {
            $site = Site::query()->where('id', $id)->first();
            if (!$site)
                return self::responseWithError('Site details not found, please try again later.');

            Site::query()->where('id', $id)->update([
                'archived' => 1,
            ]);
            return self::responseWithSuccess('Site details successfully archived.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function storeSiteDirectionDetails(Request $request) {
        try {
            $validator = Validator::make($request->input(), [
                'site_direction' => 'required',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();
            $site = Site::query()->where('id', $params['site_id_for_direction'])->first();
            if (!$site)
                return self::responseWithError('Site details not found, please try again later.');

            Site::query()->where('id', $params['site_id_for_direction'])->update([
                'site_direction' => $params['site_direction']
            ]);

            return self::responseWithSuccess('Site direction successfully updated.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getSiteForContact(Request $request) {
        try {
            $site = Site::query()->where('client_id', $request->input('client_id'))->get();

            $site_checkbox = '';
            foreach ($site as $row) {
                $site_checkbox .= '<label class="form-check form-check-inline me-5 is-invalid">
                        <input type="checkbox" class="form-check-input" name="contact_site[]" id="site_check_list_'.$row['id'].'" value="'.$row['id'].'">
                        <span class="fw-bold ps-2 fs-6">'.$row['site_name'].'</span>
                    </label>';
            }

            return self::responseWithSuccess('List of site checkbox', [
                'site_checkbox' => $site_checkbox,
            ]);
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getClientContact(Request $request) {
        try {
            $client_id = $request->input('client_id');
            $contact = ClientContact::query()->where('client_id', $client_id)
                ->when(request('status') != 'All', function ($q) {
                    return $q->where('archived', request('status'));
                })
                ->get();

            $array  = [];
            if ($contact) {
                foreach ($contact as $row) {

                    $site = Site::query()->whereIn('id', explode(',', $row['site_id']))->get();
                    $site_text = '';
                    foreach ($site as $s_row) {
                        $site_text .= '<span class="badge badge-info me-1 mt-1">'.$s_row['site_name'].'</span>';
                    }

                    $array[] = [
                        'name' => $row['first_name'].' '.$row['last_name'],
                        'primary_contact_number' => $row['primary_contact_number'],
                        'secondary_contact_number' => $row['secondary_contact_number'],
                        'email' => '<a href="mailto:'.$row['email'].'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" id="contact_email">
                                    <i class="fs-2 lab la-telegram-plane"></i>
                                    </a>',
                        'site' => $site_text,
                        'action'   => $this->contact_action($row['id'], $row['archived']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($contact),
                'recordsFiltered'   => count($contact),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function contact_action($id,$archived) {
        $action = '';
        if($archived == 0) {
            $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="delete_client_contact" data-contact_id="'.$id.'">
                    <i class="fs-2 las la-archive"></i>
                </a>';
        }

        $action .=  '<a href="'.url('view-client-contact/'.$id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_client_contact" data-contact_id="'.$id.'">
                <i class="fs-2 las la-arrow-right"></i>
            </a>';

        return $action;
    }

    public function storeClientContactDetails(Request $request) {
        try {
            $validator = Validator::make($request->input(), [
                'contact_first_name'=> 'required',
                'contact_last_name' => 'required',
                'contact_email'     => 'required|email',
                'contact_telephone_one' => 'required|numeric',
                'contact_site' => 'required',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();

            $array = [
                'client_id' => $params['contact_client_id'],
                'first_name'=> $params['contact_first_name'],
                'last_name' => $params['contact_last_name'],
                'email'     => $params['contact_email'],
                'primary_contact_number' => $params['contact_telephone_one'],
                'secondary_contact_number' => $params['contact_telephone_two'],
                'site_id'   => ($request->has('contact_site')) ? implode(',', $params['contact_site']) : null,
            ];

            if ($params['contact_id'] == 0) {
                ClientContact::query()->create($array);
                $message = 'Client contact details successfully stored.';
            } else {
                $contact = ClientContact::query()->where('id', $params['contact_id'])->first();
                if (!$contact)
                    return self::responseWithError('Contact details not found, please try again later.');

                ClientContact::query()->where('id', $params['contact_id'])->update($array);
                $message = 'Client contact details successfully updated.';
            }

            return self::responseWithSuccess($message);
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function viewClientContact($id) {
        $contact = ClientContact::query()->where('id', $id)
            ->with('client_details')
            ->first();

        $site_checkbox  = '';
        $selected_sites = explode(',', $contact['site_id']);
        $site           = Site::query()->where('client_id', $contact['client_id'])->get();
        foreach ($site as $row) {
            $checked = (in_array($row['id'], $selected_sites)) ? 'checked' : '';
            $site_checkbox .= '<label class="form-check form-check-inline me-5 is-invalid">
                        <input type="checkbox" class="form-check-input" name="contact_site[]" id="site_check_list_'.$row['id'].'" value="'.$row['id'].'" '.$checked.'>
                        <span class="fw-bold ps-2 fs-6">'.$row['site_name'].'</span>
                    </label>';
        }

        return view('clients.view_contact', compact(['contact','site_checkbox']));
    }

    public function deleteClientContactAction($id) {
        try {
            $contact = ClientContact::query()->where('id', $id)->first();
            if (!$contact)
                return self::responseWithError('Contact details not found, please try again later.');

            ClientContact::query()->where('id', $id)->update([
                'archived' => 1,
            ]);

            return self::responseWithSuccess('Contact details successfully archived.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateClientDocumentDetails(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'required_document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ], [
                'required_document_file.required' => 'The document field is required.',
                'required_document_file.max' => 'Selected file size is higher than 10MB.',
                'required_document_file.mimes' => 'Selected file extension is not supported. Only jpg, png, jpeg, pdf are allowed.',
            ]);

            $message = $validator->errors()->messages();
            if ($message) {
                return self::validationError($message);
            }

            $params = $request->input();
            $client_id = $params['upload_required_document_client_id'];
            $client = Client::query()->where('id', $client_id)->first();
            if (!$client) {
                return self::responseWithError('Client details not found please try again.');
            }

            if ($params['upload_required_document_id'] == 0) {
                $document_file_upload = FileHelper::file_upload($request->file('required_document_file'), 'workers/client_document');
                ClientDocument::query()->create([
                    'client_id'           => $client['id'],
                    'document_file'       => $document_file_upload['file_name'],
                    'document_file_type'  => $document_file_upload['file_type'],
                    'document_file_title' => strtoupper($params['upload_required_document_title']),
                    'expiry_date'         => ($params['required_document_expiry_date']) ? date('Y-m-d', strtotime($params['required_document_expiry_date'])) : null,
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => Carbon::now()
                ]);
            } else {
                $clientDocument = ClientDocument::query()->where('id', $params['upload_required_document_id'])->first();
                if (!$clientDocument) {
                    return self::responseWithError('Client document not found please try again later.');
                }

                FileHelper::file_remove($clientDocument['document_file'], 'workers/client_document');
                $document_file_upload = FileHelper::file_upload($request->file('required_document_file'), 'workers/client_document');

                ClientDocument::query()->where('id', $params['upload_required_document_id'])->update([
                    'client_id'           => $client['id'],
                    'document_file'       => $document_file_upload['file_name'],
                    'document_file_type'  => $document_file_upload['file_type'],
                    'document_file_title' => strtoupper($params['upload_required_document_title']),
                    'expiry_date'         => ($params['required_document_expiry_date']) ? date('Y-m-d', strtotime($params['required_document_expiry_date'])) : null,
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => Carbon::now()
                ]);
            }
            ClientHelper::automaticClientActive($client_id);
            return self::responseWithSuccess('Document successfully uploaded.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateClientOtherDocumentDetails(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'other_document_title' => 'required',
                'other_document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ], [
                'other_document_title.required' => 'The document title field is required.',
                'other_document_file.required' => 'The document field is required.',
                'other_document_file.max' => 'Selected file size is higher than 10MB.',
                'other_document_file.mimes' => 'Selected file extension is not supported. Only jpg, png, jpeg, pdf are allowed.',
            ]);

            $message = $validator->errors()->messages();
            if ($message) {
                return self::validationError($message);
            }

            $params = $request->input();
            $client = Client::query()->where('id', $params['upload_other_document_client_id'])->first();
            if (!$client) {
                return self::responseWithError('Client details not found please try again.');
            }

            if ($params['upload_other_document_id'] == 0) {

                $document_file_upload = FileHelper::file_upload($request->file('other_document_file'), 'workers/client_document');
                ClientDocument::query()->create([
                    'client_id' => $client['id'],
                    'document_file' => $document_file_upload['file_name'],
                    'document_file_type' => $document_file_upload['file_type'],
                    'document_file_title' => $params['other_document_title'],
                    'expiry_date' => ($params['other_document_expiry_date']) ? date('Y-m-d', strtotime($params['other_document_expiry_date'])) : null,
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => Carbon::now()
                ]);
            }else{
                $clientDocument = ClientDocument::query()->where('id', $params['upload_other_document_id'])->first();
                if (!$clientDocument) {
                    return self::responseWithError('Client document not found please try again later.');
                }

                FileHelper::file_remove($clientDocument['other_document_file'], 'workers/client_document');
                $document_file_upload = FileHelper::file_upload($request->file('other_document_file'), 'workers/client_document');

                ClientDocument::query()->where('id', $params['upload_other_document_id'])->update([
                    'client_id'           => $client['id'],
                    'document_file'       => $document_file_upload['file_name'],
                    'document_file_type'  => $document_file_upload['file_type'],
                    'document_file_title' => strtoupper($params['other_document_title']),
                    'expiry_date' => ($params['other_document_expiry_date']) ? date('Y-m-d', strtotime($params['other_document_expiry_date'])) : null,
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => Carbon::now()
                ]);
            }

            return self::responseWithSuccess('Document successfully uploaded.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteClientDocumentAction($document_id) {
        try {
            $document = ClientDocument::query()->where('id', $document_id)->first();
            if (!$document)
                return self::responseWithError('Document not found please try again.');

            $path = public_path('workers/client_document/'.$document['document_file']);
            if (file_exists($path))
                unlink($path);

            ClientDocument::query()->where('id', $document_id)->delete();
            return self::responseWithSuccess('Document successfully deleted.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function deleteClientAction($id) {
        try {
            $client = Client::query()->where('id', $id)->first();
            if(!$client)
                return self::responseWithError('Client details not found, please try again.');

            ClientJob::query()->where('client_id', $id)->update([
                'archived' => '1',
            ]);

            $client->delete();

            return self::responseWithSuccess('Client successfully archived.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getClientJobs(Request $request) {
        try {
            $jobs = ClientJob::query()
                /*->where('client_id', $request->input('client_id'))*/
                ->when(request('client_id') != 0, function ($q) { return $q->where('client_id', request('client_id')); })
                ->when(request('status') != null && request('status') != 'All', function ($q) {
                    return $q->where('archived', request('status')
                    );
                })
                ->with(['site_details', 'client_details'])->orderBy('name', 'asc')
                ->get();
            $array  = [];

            if ($jobs) {
                foreach ($jobs as $row) {
                    $array[] = [
                        'job_id'    => $row['id'],
                        'name'      => $row['name'],
                        'client'    => ($row['client_details']) ? $row['client_details']['company_name'] : '-',
                        'site'      => ($row['site_details']) ? $row['site_details']['site_name'] : '-',
                        'start_date'=> date('d M Y', strtotime($row['start_date'])),
                        'status'    => ($row['archived'] == '0') ? 'Active' : 'Archived',
                        'action'    => $this->job_action($row['id'], $row['client_id'], $row['site_id'], $row['archived']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($jobs),
                'recordsFiltered'   => count($jobs),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function job_action($id, $client_id, $site_id, $archived) {
        /*<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" data-kt-quotation-table-filter="delete_row" id="delete_client_contact" data-contact_id="'.$id.'">
                    <span class="svg-icon svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black"></path>
                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black"></path>
                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black"></path>
                        </svg>
                    </span>
                </a>*/

        if ($archived == 0) {
            $action = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1 archive_action" id="archive_client_job" data-job_id="'.$id.'" data-status="1" data-text="You want to archive this job!" data-btn_text="Yes, archive!">
                                <i class="fs-2 las la-archive"></i>
                </a>';
        } else {

            $action = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1 archive_action" id="un_archive_client_job" data-job_id="'.$id.'"  data-status="0" data-text="You want to active this job!" data-btn_text="Yes, active!">
                    <span class="svg-icon svg-icon-2">
                        <i class="fs-2 las la-undo"></i>
                    </span>
                </a>';
        }

        $action .= '
        <a href="'.url('assignment-management?tag='.$client_id.'.'.$site_id.'.'.$id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_client_job" data-job_id="'.$id.'">
            <i class="fs-2 las la-calendar"></i>
        </a>
        <a href="'.url('view-client-job/'.$id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_client_job" data-job_id="'.$id.'">
                         <i class="fs-2 las la-arrow-right"></i>
                </a>';


        return $action;
    }

    public function storeClientJobDetails(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'job_name'                  => 'required',
                'job_client_id'             => 'required',
                'job_site'                  => 'required',
                'assignment_schedule'       => 'required|file|mimes:pdf|max:10240',
                'job_description'           => 'required',
                'job_health_and_safety_information' => 'required',
                'job_start_date'            => 'required',
                'job_default_shift_time'    => 'required',
                'job_default_shift_length_hr'   => 'required|numeric|min:1|max:23',
                'job_default_shift_length_min'  => 'required|numeric|min:0|max:60',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();

            $assignment_schedule_upload = FileHelper::file_upload($request->file('assignment_schedule'), 'workers/client_job');
            $array = [
                'client_id'                 => $params['job_client_id'],
                'site_id'                   => $params['job_site'],
                'name'                      => $params['job_name'],
                'start_date'                => date('Y-m-d', strtotime($params['job_start_date'])),
                'end_date'                  => ($params['job_end_date']) ? date('Y-m-d', strtotime($params['job_end_date'])) : null,
                'default_shift_start_time'  => $params['job_default_shift_time'],
                'default_shift_length_hr'   => $params['job_default_shift_length_hr'],
                'default_shift_length_min'  => $params['job_default_shift_length_min'],
                'default_shift_length'      => ((int)$params['job_default_shift_length_hr'] * 60) + (int)$params['job_default_shift_length_min'],
                'assignment_schedule'       => $assignment_schedule_upload['file_name'],
                'reference'                 => $request->has('reference') ? $params['reference'] : null,
                'description'               => $params['job_description'],
                'health_and_safety_information' => $params['job_health_and_safety_information'],
            ];

            ClientJob::query()->create($array);
            return self::responseWithSuccess('Client job details successfully stored.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function ArchiveClientJobAction($id, $status) {
        try {

            if (in_array($status, [0,1])) {

                $clientJob = ClientJob::query()->where('id', $id)->with('client_details')->first();
                if ($status == 0 && $clientJob['client_details']['deleted_at'] != null) {
                    return self::responseWithError('You cannot unarchived a job for an archived client.');
                }

                ClientJob::query()->where('id', $id)->update([
                    'archived' => $status,
                ]);

                $message = ($status == 0) ? 'Client job successfully active.' : 'Client job successfully archive.';
            } else {
                $message = 'Client job successfully deleted.';
            }

            return self::responseWithSuccess($message);
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function viewClientJob($id) {
        $job = ClientJob::query()->where('id', $id)->with(['client_details', 'site_details', 'upcoming_pay_rate_details', 'pay_rate_multiple'])->first();
        $site = Site::query()->where('client_id', $job['client_id'])->get();
        $job_line = JobLine::withTrashed()->where('job_id', $id)->first();

        $carbon = Carbon::now();
        $week = $carbon->addWeek();
        $week_number = $week->isoWeek;
        $week_year = $week->isoWeekYear;

        $minDate = JobHelper::validFromMinDate($id);
        $jobLineTextBox = JobHelper::preparedJobLineTextBox($id);
        $assignedGroupIds = GroupWithJob::query()->where('job_id', $id)
            ->pluck('group_id')
            ->toArray();

        $group = Group::query()
            ->select(['id','name'])
            ->where('consultant_id', Auth::id())
            ->whereNotIn('id', $assignedGroupIds)
            ->orderBy('name', 'asc')
            ->get();

        $linkedGroups = GroupWithJob::query()->where('job_id', $id)
            ->with([
                'groups' => function ($query) {
                    $query->withCount([
                        'workers as active_members_count' => function ($q) {
                            $q->where('status', 'Active')
                                ->whereHas('latest_end_date_rights_to_work_details', function ($sub) {
                                    $sub->whereDate('end_date', '>=', Carbon::today());
                                });
                        },

                        'workers as leavers_count' => function ($q) {
                            $q->where('status', 'Leaver')
                                ->whereHas('latest_end_date_rights_to_work_details', function ($sub) {
                                    $sub->whereDate('end_date', '>=', Carbon::today());
                                });
                        },

                        'workers as archived_count' => function ($q) {
                            $q->where('status', 'Archived')
                                ->whereHas('latest_end_date_rights_to_work_details', function ($sub) {
                                    $sub->whereDate('end_date', '>=', Carbon::today());
                                });
                        },

                        'workers as no_rtw_count' => function ($q) {
                            $q->where(function ($sub) {
                                $sub->whereDoesntHave('latest_end_date_rights_to_work_details')
                                    ->orWhereHas('latest_end_date_rights_to_work_details', function ($inner) {
                                        $inner->whereDate('end_date', '<', Carbon::today());
                                    });
                            });
                        },
                    ]);
                }
            ])
            ->get()
            ->toArray();

        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('clients.view_client_job', compact(['job', 'site', 'job_line', 'week_number', 'week_year', 'minDate', 'jobLineTextBox', 'group', 'linkedGroups', 'costCentre']));
    }

    public function updateClientJobBasicDetails(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'job_name'                  => 'required',
                'job_site'                  => 'required',
                'job_description'           => 'required',
                'job_health_and_safety_information'=> 'required',
                'job_start_date'            => 'required',
                'job_default_shift_time'    => 'required',
                'job_default_shift_length_hr'   => 'required|integer|min:1|max:23',
                'job_default_shift_length_min'  => 'required|integer|min:0|max:60',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();

            $clientJob = ClientJob::query()->where('id', $params['job_id'])->first();
            if (!$clientJob)
                return self::responseWithError('Client job not found, please try again later.');

            if ($request->has('assignment_schedule')) {
                FileHelper::file_remove($clientJob['assignment_schedule'], 'workers/client_job');
                $assignment_schedule_upload = FileHelper::file_upload($request->file('assignment_schedule'), 'workers/client_job');
                $assignment_schedule = $assignment_schedule_upload['file_name'];
            } else {
                $assignment_schedule = $clientJob['assignment_schedule'];
            }

            $array = [
                'site_id'                   => $params['job_site'],
                'name'                      => $params['job_name'],
                'description'               => $params['job_description'],
                'health_and_safety_information' => $params['job_health_and_safety_information'],
                'directions'                => $params['job_directions'],
                'start_date'                => date('Y-m-d', strtotime($params['job_start_date'])),
                'end_date'                  => ($request->has('job_end_date') && $params['job_end_date']) ? date('Y-m-d', strtotime($params['job_end_date'])) : null,
                'default_shift_start_time'  => $params['job_default_shift_time'],
                'default_shift_length_hr'   => $params['job_default_shift_length_hr'],
                'default_shift_length_min'  => $params['job_default_shift_length_min'],
                'default_shift_length'      => ((int)$params['job_default_shift_length_hr'] * 60) + (int)$params['job_default_shift_length_min'],
                'assignment_schedule'       => $assignment_schedule,
                'default_number_workers'    => $params['job_default_number_workers'],
                'reference'                 => $request->has('reference') ? $params['reference'] : null,
            ];

            ClientJob::query()->where('id', $params['job_id'])->update($array);
            return self::responseWithSuccess('Job basic details successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    /*public function updateClientJobPayRateDetails(Request $request) {

        try {
            $params = $request->input();
            $rules = [
                'default_pay_rate' => 'required|numeric|min:0',
                'default_charge_rate' => 'required|numeric|min:0',
                'overtime_pay_rate' => 'nullable|numeric|min:0',
                'overtime_charge_rate' => 'nullable|numeric|min:0',
                'weekly_ot_threshold' => 'nullable|integer|min:0',
            ];

            // Conditional validation: if overtime pay rate is set, overtime hours threshold and charge rate are required
            if (isset($params['overtime_pay_rate']) && $params['overtime_pay_rate'] != '') {
                $rules['weekly_ot_threshold'] = 'required|integer|min:0';
                $rules['overtime_charge_rate'] = 'required|numeric|min:0';
            }

            $validator = Validator::make($params, $rules);
            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            ClientJobPayRate::query()->updateOrCreate([
                  'id' => $params['pay_rate_id'],
                ],
                [
                    'job_id' => $params['pay_rate_job_id'],
                    'base_pay_rate' => $params['default_pay_rate'],
                    'base_charge_rate' => $params['default_charge_rate'],
                    'default_overtime_pay_rate' => $params['overtime_pay_rate'],
                    'default_overtime_charge_rate' => $params['overtime_charge_rate'],
                    'default_overtime_hours_threshold' => $params['weekly_ot_threshold']
                ]
            );

            return self::responseWithSuccess('Pay rate successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }*/

    public function searchClientJobWorker(Request $request) {
        try {
            $params  = $request->input();

            if (!$params['keyword']['term'])
                return self::responseWithError('Please enter keyword to search worker');

            $jobWorker = ClientJobWorker::query()->where('job_id', $params['job_id'])
                ->whereNull('declined_at')
                ->whereNull('archived_at')
                ->get()
                ->pluck('worker_id');

            $searchTerms = explode(' ', $params['keyword']['term']);
            $worker = Worker::query()
                ->select(['id', 'first_name', 'middle_name', 'last_name', 'date_of_birth'])
                ->with('worker_cost_center')
                ->whereNotNull('email_verified_at')
                ->where('status', 'Active')
                ->where(function ($query) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $query->where(function ($query) use ($term) {
                            $query->where('first_name', 'LIKE', '%'.$term.'%')
                                ->orWhere('middle_name', 'LIKE', '%'.$term.'%')
                                ->orWhere('last_name', 'LIKE', '%'.$term.'%');
                        });
                    }
                })
                ->when(request('associated_cost_center') != null, function ($query) use ($params) {
                    $query->whereHas('worker_cost_center', function ($subQuery) use ($params) {
                        $subQuery->whereIn('cost_center', $params['associated_cost_center']);
                    });
                })
                ->whereNotIn('id', $jobWorker)
                ->get();

            $array = [];
            if ($worker) {
                foreach ($worker as $row) {
                    $array[] = [
                        'id'    => $row['id'],
                        'name'  => $row['first_name'].' '.$row['middle_name'].' '.$row['last_name'].' - '.date('d/m/Y', strtotime($row['date_of_birth'])),
                    ];
                }
            }

            return self::responseWithSuccess('Worker details', $array);
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function storeClientJobWorker(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->input(), [
                //'associated_cost_center'    => 'required',
                'job_worker_name'           => 'required',
                'invitation_type'           => 'required',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();
            $ClientJobWorker = ClientJobWorker::query()->create([
                'job_id'                    => $params['job_worker_id'],
                'worker_id'                 => $params['job_worker_name'],
                'associated_cost_center'    => $request->has('associated_cost_center') ? implode(', ', $params['associated_cost_center']) : null,
                'invitation_type'           => $params['invitation_type'],
                'confirmed_at'              => ($params['invitation_type'] == '2') ? Carbon::now() : null,
                'confirmed_by_admin_user_id'=> ($params['invitation_type'] == '2') ? Auth::id() : null,
            ]);

            /*--- BEGIN MAIL CODE ---*/
            ClientHelper::workerAddedIntoJobSendMail($params['job_worker_name'], $params['job_worker_id'], $ClientJobWorker['id'], $params['invitation_type']);
            /*--- END MAIL CODE ---*/

            DB::commit();
            return self::responseWithSuccess('Worker successfully added in job.');
        } catch (Exception $e) {

            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function storeClientJobWorkerMultiple(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->input(), [
                'job_worker_name'           => 'required',
                'invitation_type'           => 'required',
            ]);

            if ($validator->errors()->messages()) {
                return self::validationError($validator->errors()->messages());
            }

            $params = $request->input();
            $workerIds = Worker::query()->select('id')->whereNotNull('email_verified_at')->whereIn('id', $params['job_worker_name'])->pluck('id')->toArray();

            $alreadyAddedWorkerIds = ClientJobWorker::query()->where('job_id', $params['job_worker_id'])
                ->whereIn('worker_id', $workerIds)
                ->whereNull('declined_at')
                ->whereNull('archived_at')
                ->pluck('worker_id')->toArray();

            if ($alreadyAddedWorkerIds) {
                $workerIds = array_diff($workerIds, $alreadyAddedWorkerIds);
                $workerIds = array_values($workerIds);
            }

            if (!$workerIds) {
                /*add_type -> single / Multiple*/
                $message = (isset($params['add_type']) && $params['add_type'] == 'single') ? 'Worker already added in this job' : 'Workers not available to added in the selected job.';
                throw new \Exception($message);
            }

            $payRateDetails = ClientJobPayRate::query()->where('job_id', $params['job_worker_id'])->first();
            if (!$payRateDetails) {
                throw new \Exception('Pay rate details not available, please insert pay rate details in this job.');
            }

            foreach ($workerIds as $job_worker_name) {

                $ClientJobWorker = ClientJobWorker::query()->where('job_id', $params['job_worker_id'])
                    ->where('worker_id', $job_worker_name)
                    ->first();

                if ($ClientJobWorker) {
                    ClientJobWorker::query()->where('id', $ClientJobWorker['id'])->update([
                            'associated_cost_center'    => $request->has('associated_cost_center') ? implode(', ', $params['associated_cost_center']) : null,
                            'invitation_type'           => $params['invitation_type'],
                            'confirmed_at'              => ($params['invitation_type'] == '2') ? Carbon::now() : null,
                            'confirmed_by_admin_user_id'=> ($params['invitation_type'] == '2') ? Auth::id() : null,
                            'declined_at'               => null,
                            'archived_at'               => null
                        ]);
                } else {
                    $ClientJobWorker = ClientJobWorker::query()->create([
                        'job_id'                    => $params['job_worker_id'],
                        'worker_id'                 => $job_worker_name,
                        'associated_cost_center'    => $request->has('associated_cost_center') ? implode(', ', $params['associated_cost_center']) : null,
                        'invitation_type'           => $params['invitation_type'],
                        'confirmed_at'              => ($params['invitation_type'] == '2') ? Carbon::now() : null,
                        'confirmed_by_admin_user_id'=> ($params['invitation_type'] == '2') ? Auth::id() : null,
                    ]);
                }

                /*--- BEGIN MAIL CODE ---*/
                ClientHelper::workerAddedIntoJobSendMail($params['job_worker_name'], $params['job_worker_id'], $ClientJobWorker['id'], $params['invitation_type']);
                /*--- END MAIL CODE ---*/
            }

            DB::commit();
            return self::responseWithSuccess('Worker successfully added in job.');
        } catch (\Exception $e) {

            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function getClientJobWorker(Request $request) {
        try {

            $todayDate = Carbon::now()->toDateString();
            $query = ClientJobWorker::query()
                ->where('job_id', $request->input('job_id'))
                ->with(['worker', 'rightsToWork']);

            if ($request->input('status') === 'available') {
                $query->whereNotNull('confirmed_at')
                    ->whereNull('archived_at')
                    ->whereHas('worker', function ($query) {
                        $query->where('status', 'active')
                            ->where('suspend', 'No');
                    })
                    ->whereHas('rightsToWork', function ($query) {
                        $query->whereNotNull('end_date');
                    });

            } elseif ($request->input('status') === 'unconfirmed') {
                $query->whereNotNull('invited_at')
                    ->whereNull('confirmed_at')
                    ->whereNull('declined_at');

            } elseif ($request->input('status') === 'unavailable') {
                $query->where(function ($query1) use ($todayDate) {
                    $query1->orWhereNotNull('declined_at')
                        ->orWhereNotNull('archived_at')
                        ->orWhereHas('worker', function ($query2) {
                            $query2->whereIn('status', ['Leaver', 'Archived'])
                                ->orWhere('suspend', 'Yes');
                        })
                        ->whereHas('rightsToWork', function ($query3) use ($todayDate) {
                            $query3->whereDate('end_date', '>=', $todayDate)
                                ->latest('end_date');
                        });
                    });

            }

            $worker = $query->get()->toArray();
            $array  = [];
            if ($worker) {
                foreach ($worker as $row) {


                    $latestRTWExpiryDate = RightToWorkHelper::getLatestDate($row['rights_to_work']);
                    $status = ($row['worker']) ? $row['worker']['status'] : '-';
                    $statusText = ($row['worker']) ? ($row['worker']['suspend'] == 'Yes') ? $status.' (<span class="text-danger">suspended</span>)' : $status : $status;
                    $worker_name = ($row['worker']) ? $row['worker']['first_name'].' '.$row['worker']['middle_name'].' '.$row['worker']['last_name'] : '-';
                    $array[] = [
                        'name'          => $worker_name,
                        'worker_id'     => ($row['worker']) ? $row['worker']['worker_no'] : '',
                        //'dob'           => ($row['worker']) ? date('d-m-Y', strtotime($row['worker']['date_of_birth'])) : '-',
                        'status'        => $statusText,
                        'invited_at'    => ($row['invitation_type'] == '1') ? date('d-m-Y H:i', strtotime($row['invited_at'])) : '-',
                        'declined_at'   => ($row['declined_at']) ? date('d-m-Y H:i', strtotime($row['declined_at'])) : '-',
                        'confirmed_at'  => ($row['confirmed_at']) ? date('d-m-Y H:i', strtotime($row['confirmed_at'])) : '-',
                        'rtw_expires'   => ($latestRTWExpiryDate) ? date('d-m-Y', strtotime($latestRTWExpiryDate)) : '-',
                        'action'        => $this->job_worker_action($row['id'], $row['worker_id'], $worker_name, $row['confirmed_at'], $row['archived_at'], $row['declined_at']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($worker),
                'recordsFiltered'   => count($worker),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getClientJobWorkerTransport(Request $request) {
        try {
            $worker = ClientJobWorker::query()
                ->where('job_id', $request->input('job_id'))
                ->with(['worker.preferred_pickup_point', 'rightsToWork'])
                ->whereNotNull('confirmed_at')
                ->whereNull('archived_at')
                ->whereHas('worker', function ($query) {
                    $query->where('status', 'active')
                        ->where('suspend', 'No')
                        ->where('proforce_transport', 'Yes');
                })
                ->whereHas('rightsToWork', function ($query) {
                    $query->whereNotNull('end_date');
                })
                ->get()
                ->toArray();

            $array  = [];
            if ($worker) {
                $pickupPoints = PickUpPoint::query()->get();
                foreach ($worker as $row) {
                    //$agreedPickupPoint = $row['agreed_pickup_point'] ?? '';
                    $preferredPickup = $row['worker']['preferred_pickup_point'] ?? null;
                    if ($row['agreed_pickup_point'] == '0' || $row['agreed_pickup_point'] == '00' || $row['agreed_pickup_point']) {
                        $agreedPickupPoint = $row['agreed_pickup_point'];
                    } else {
                        $agreedPickupPoint = ($preferredPickup)
                            ? $preferredPickup['id']
                            : '';
                    }

                    $selectDrp = '<select name="agreed_pickup_point_'.$row['id'].'" id="agreed_pickup_point_'.$row['id'].'" class="agreed_pickup_point_drp form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">';

                    $selectDrp .= '<option value="" '.($agreedPickupPoint === "" ? 'selected' : '').'>Select...</option>';
                    $selectDrp .= '<option value="0" '.($agreedPickupPoint === "0" ? 'selected' : '').'>None (no pickup required)</option>';
                    $selectDrp .= '<option value="00" '.($agreedPickupPoint === "00" ? 'selected' : '').'>Same as preferred</option>';

                    foreach ($pickupPoints as $pickup_point_row) {
                        $selected = ($agreedPickupPoint == $pickup_point_row->id) ? 'selected' : '';
                        $selectDrp .= '<option value="'.$pickup_point_row->id.'" '.$selected.'>'.$pickup_point_row->name.' - ///'.$pickup_point_row->what_three_words_locator.'</option>';
                    }

                    $selectDrp .= '</select>';

                    $preferred_pickup_point = $preferredPickup
                        ? $preferredPickup['name'].' - ///'.$preferredPickup['what_three_words_locator']
                        : '-';

                    $inputBox = '<input type="hidden" name="record_ids[]" id="record_id_'.$row['id'].'" value="'.$row['id'].'" />';
                    $array[] = [
                        'record_id' => $row['id'],
                        'name' => isset($row['worker']) ? $row['worker']['first_name'].' '.$row['worker']['middle_name'].' '.$row['worker']['last_name'] : '-',
                        'preferred_pickup_point' => $preferred_pickup_point,
                        'agreed_pickup_point' => $selectDrp.$inputBox,
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($worker),
                'recordsFiltered'   => count($worker),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateWorkerTransportDetails(Request $request) {
        try {
            $params = $request->input();
            $worker_transport = json_decode($params['worker_transport'], true);
            if (!$worker_transport) {
                return self::responseWithError('No any worker available for update.');
            }

            foreach ($worker_transport as $row) {
                ClientJobWorker::query()->where('id', $row['record_id'])->update([
                    'agreed_pickup_point' => $row['agreed_pickup_point']
                ]);
            }
            return self::responseWithSuccess('Worker transport detail successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function job_worker_action($id, $worker_id, $worker_name, $confirmed_at, $archived_at, $declined_at) {
        $action = '';
        if ($confirmed_at || $declined_at) {
            if (!$archived_at) {
                $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="archive_job_worker" data-job_worker_id="' . $id . '" data-worker_name="'.$worker_name.'">
                        <span class="svg-icon svg-icon-2">
                        <i class="fs-2 las la-unlink"></i>
                        </span>
                    </a>';
            } else{
                $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="relink_job_worker" data-job_worker_id="' . $id . '" data-worker_name="'.$worker_name.'">
                    <i class="fs-2 las la-undo"></i>
                </a>';
            }
        } else {

            $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1 confirmed_action" id="confirmed_job_worker" data-job_worker_id="' . $id . '">
                    <i class="fs-2 las la-check"></i>
                </a>

                <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1 declined_action" id="declined_job_worker" data-job_worker_id="' . $id . '">
                    <i class="fs-2 las la-times"></i>
                </a>
                ';
        }

        $action .= '<a href="'.url('view-worker-details/'.$worker_id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_worker" target="_blank">
                    <span class="svg-icon svg-icon-2">
                    <i class="fs-2 las la-arrow-right"></i>
                    </span>
                </a>';


        return $action;
    }

    public function archiveClientJobWorker($id) {
        try {
            $ClientJobWorker = ClientJobWorker::query()->where('id', $id)->first();
            if ($ClientJobWorker['archived_at'])
                return self::responseWithError('This worker is already archived.');

            $ClientJobWorker->update([
                'archived_at' => Carbon::now(),
            ]);
            return self::responseWithSuccess('Associate successfully unlinked.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function confirmClientJobWorkerAdmin($id, $confirm_by, $status) {
        try {
            $ClientJobWorker = ClientJobWorker::query()->where('id', $id)->first();
            if (!$ClientJobWorker['confirmed_at']) {
                if ($status == 1) {
                    $ClientJobWorker->update([
                        'confirmed_at' => Carbon::now(),
                        'confirmed_by_admin_user_id' => $confirm_by,
                    ]);
                    $message = 'Worker invitation successfully confirm.';
                } else {
                    $ClientJobWorker->update([
                        'declined_at' => Carbon::now(),
                        'confirmed_by_admin_user_id' => $confirm_by,
                    ]);
                    $message = 'Worker invitation successfully decline.';
                }
            }
            return self::responseWithSuccess($message);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function confirmClientJobWorker($id, $confirm_by, $status) {
        try {
            $ClientJobWorker = ClientJobWorker::query()->where('id', $id)->first();
            if (!$ClientJobWorker['confirmed_at']) {
                if ($status == 1) {
                    $ClientJobWorker->update([
                        'confirmed_at' => Carbon::now(),
                        'confirmed_by_admin_user_id' => $confirm_by,
                    ]);
                    //$message = 'Worker invitation successfully confirm.';
                } else {
                    $ClientJobWorker->update([
                        'declined_at' => Carbon::now(),
                        'confirmed_by_admin_user_id' => $confirm_by,
                    ]);
                    //$message = 'Worker invitation successfully decline.';
                }
            }
            return view('clients.job_invitation_after_action');
            //return self::responseWithSuccess($message);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function uploadClientLogoPic(Request $request) {
        try {
            $client = Client::query()->select('id', 'company_logo')->where('id', $request->input('client_id'))->first();
            if (!$client)
                return self::responseWithError('Client not found, please try again later.');

            if ($request->hasFile('client_logo_pic')) {
                if ($client['company_logo']) {
                    FileHelper::file_remove($client['company_logo'], 'workers/client_document');
                }

                $upload = FileHelper::file_upload($request->file('client_logo_pic'), 'workers/client_document');
                $client->update([
                    'company_logo' => $upload['file_name'],
                ]);

                return self::responseWithSuccess('company logo successfully uploaded.');
            } else {
                if ($request->input('avatar_remove') == 1) {
                    if ($client['company_logo']) {
                        FileHelper::file_remove($client['company_logo'], 'workers/client_document');
                    }

                    $client->update([
                        'company_logo' => null,
                    ]);

                    return self::responseWithSuccess('company logo successfully removed.');
                } else {
                    return self::responseWithError('Please select a company logo.');
                }
            }
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function storeClientNoteDetails(Request $request){
        try {
            $validator = Validator::make($request->input(), [
                'note_text' => 'required',
            ]);

            if($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $type = $request->input('note_type');
            Note::query()->create([
                $type.'_id' => $request->input('action_id'),
                'user_id'   => Auth::id(),
                'note_type' => 'General',
                'note_text' => $request->input('note_text'),
                'type'      => ucfirst($type),
            ]);
            return self::responseWithSuccess('Note successfully created');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getClientNote(Request $request){
        try {
            $type = $request->input('note_type');
            $note = Note::query()->where($type.'_id', $request->input('action_id'))
                ->where('type', ucfirst($type))
                ->with('user_details')
                ->get();

            $array  = [];
            if ($note) {
                foreach ($note as $row) {
                    $created_by = ($row['user_details'])
                        ? $row['user_details']['name']
                        : '';

                    $array[] = [
                        'type' => '<span class="badge bg-success">'.$row['type'].'</span>',
                        'note'  => $row['note_text'],
                        'created_by' => '<span class="badge bg-gray-600">'.$created_by.'</span>',
                        'created_at'   => '<span class="badge bg-gray-600">'.date('d-m-Y h:i:s a', strtotime($row['created_at'])).'</span>',
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($note),
                'recordsFiltered'   => count($note),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    /*public function createLocationLogs($params, $update_id) {
        $location_details = Client::query()->select(['location_name' ,'location_address_line_one' ,'location_address_line_two', 'location_city_town', 'location_county', 'location_latitude', 'location_longitude'])->where('id', $update_id)->first()->toArray();
        $name               = explode('~~~~~', $location_details['location_name']);
        $address_line_one   = explode('~~~~~', $location_details['location_address_line_one']);
        $address_line_two   = explode('~~~~~', $location_details['location_address_line_two']);
        $city_town          = explode('~~~~~', $location_details['location_city_town']);
        $county             = explode('~~~~~', $location_details['location_county']);
        $latitude           = explode('~~~~~', $location_details['location_latitude']);
        $longitude          = explode('~~~~~', $location_details['location_longitude']);

        if($name) {
            foreach ($params['location_name'] as $key => $nRow) {
                $oldData = [
                    'location_name'             => (isset($name[$key])) ? $name[$key] : '',
                    'location_address_line_one' => (isset($address_line_one[$key])) ? $address_line_one[$key] : '',
                    'location_address_line_two' => (isset($address_line_two[$key])) ? $address_line_two[$key] : '',
                    'location_city_town'        => (isset($city_town[$key])) ? $city_town[$key] : '',
                    'location_county'           => (isset($county[$key])) ? $county[$key] : '',
                    'location_latitude'         => (isset($latitude[$key])) ? $latitude[$key] : '',
                    'location_longitude'        => (isset($longitude[$key])) ? $longitude[$key] : '',
                ];

                $newData = [
                    'location_name'             => $params['location_name'][$key],
                    'location_address_line_one' => $params['location_address_line_one'][$key],
                    'location_address_line_two' => $params['location_address_line_two'][$key],
                    'location_city_town'        => $params['location_city_town'][$key],
                    'location_county'           => $params['location_county'][$key],
                    'location_latitude'         => $params['location_latitude'][$key],
                    'location_longitude'        => $params['location_longitude'][$key],
                ];

                ActivityLogs::updatesLog($update_id, 'Client update', $newData, $oldData,'Client');
            }

            return self::responseWithSuccess('Log successfully created.');
        } else {
            return self::responseWithError('Interview date not available.');
        }
    }*/

    public function getClientBooking(Request $request) {
        $dateRange = $request->input('date_range');
        if ($dateRange != 'dates') {
            $startDate = Carbon::today()->format('Y-m-d');
            $endDate = Carbon::today()->addDays($dateRange)->format('Y-m-d');
        } else {

            $startDate = Carbon::parse($request->input('booking_start_date'))->format('Y-m-d');
            $endDate = Carbon::parse($request->input('booking_end_date'))->format('Y-m-d');
        }

        $jobShiftIds = JobShift::query()->select('id')->where('job_id', $request->input('booking_job_id'))
            ->get()
            ->pluck('id');
        if ($jobShiftIds->isEmpty()) {
            return [];
        }

        $query = JobShiftWorker::query()
            ->whereIn('job_shift_id', $jobShiftIds)
            ->with(['worker','jobShift'])
            ->whereBetween('shift_date', [$startDate, $endDate]);
        $query->where(function ($q) use ($request) {
            if ($request->has('unconfirmed')) {
                $q->orWhere(function ($subQuery) {
                    $subQuery->whereNull('confirmed_at')
                        ->whereNull('declined_at')
                        ->whereNull('cancelled_at');
                });
            }

            if ($request->has('confirmed')) {
                $q->orWhereNotNull('confirmed_at');
            }

            if ($request->has('declined')) {
                $q->orWhereNotNull('declined_at');
            }

            if ($request->has('cancelled')) {
                $q->orWhereNotNull('cancelled_at');
            }
        });

        $bookings = $query->get()->toArray();

        $array  = [];
        if ($bookings) {
            foreach ($bookings as $row) {
                $array[] = [
                    'date' => Carbon::parse($row['shift_date'])->format('d-m-Y'),
                    'worker_name' => ($row['worker'])
                        ? $row['worker']['first_name'].' '.$row['worker']['middle_name'].' '.$row['worker']['last_name']
                        : '-',
                    'start_time' => ($row['job_shift'])
                        ? $row['job_shift']['start_time']
                        : '',
                    'duration' => ($row['job_shift'])
                        ? $row['job_shift']['shift_length_hr'].'h '.$row['job_shift']['shift_length_min'].'m'
                        : '',
                    'invited_at' => ($row['invited_at'] && $row['assign_type'] == 'Invitation')? '<a href="javascript:;" title="'.date('d-m-Y H:i:s', strtotime($row['invited_at'])).'"><i class="bi bi-circle-fill text-success"></i></a>' : '<i class="bi bi-circle text-muted"></i>',
                    'declined_at' => ($row['declined_at']) ? '<a href="javascript:;" title="'.date('d-m-Y H:i:s', strtotime($row['declined_at'])).'"><i class="bi bi-circle-fill text-danger"></i></a>' : '<i class="bi bi-circle text-muted"></i>',
                    'confirmed_at' => ($row['confirmed_at']) ? '<a href="javascript:;" title="'.date('d-m-Y H:i:s', strtotime($row['confirmed_at'])).'"><i class="bi bi-circle-fill text-success"></i></a>' : '<i class="bi bi-circle text-muted"></i>',
                    'cancelled_at' => ($row['cancelled_at']) ? '<a href="javascript:;" title="'.date('d-m-Y H:i:s', strtotime($row['cancelled_at'])).'"><i class="bi bi-circle-fill text-danger"></i></a>' : '<i class="bi bi-circle text-muted"></i>',
                    'action' => '<a href="#" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1">
                      <i class="fs-2 las la-arrow-right"></i>
                </a>'
                ];
            }
        }
        return [
            'draw'              => 1,
            'recordsTotal'      => count($bookings),
            'recordsFiltered'   => count($bookings),
            'data'              => $array
        ];
    }

    public function getClientTimesheet(Request $request) {
        $timesheet_jobId = $request->input('timesheet_job_id');
        $dateRange = $request->input('timesheet_date_range');

        if ($dateRange != 'between_dates') {
            $startDate = Carbon::today()->subDays($dateRange)->format('Y-m-d');
            $endDate = Carbon::today()->format('Y-m-d');
        } else {
            $startDate = Carbon::parse($request->input('timesheet_start_date'))->format('Y-m-d');
            $endDate = Carbon::parse($request->input('timesheet_end_date'))->format('Y-m-d');
        }

        $timesheets = Timesheet::query()
            ->where('job_id', $timesheet_jobId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['job_details', 'worker_details'])
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();

        $array  = [];
        if ($timesheets) {
            foreach ($timesheets as $row) {

                /*$getJobShift = JobShift::query()
                    ->where('job_id', $row['job_id'])
                    ->where('date', $row['date'])
                    ->first();*/

                $array[] = [
                    'date' => Carbon::parse($row['date'])->format('d-m-Y'),
                    'worker_name' => ($row['worker_details'])
                        ? '<a href="'.url('view-worker-details/'.$row['worker_details']['id']).'">'.$row['worker_details']['first_name'].' '.$row['worker_details']['middle_name'].' '.$row['worker_details']['last_name'].'</a>'
                        : '-',
                    'start_time' => ($row['in_time'] != '00:00:00') ? date('H:i', strtotime($row['in_time'])) : '',
                    'hours' => number_format($row['hours_worked'], 2),
                    'edited' => ($row['edited_at']) ? '<i class="bi bi-circle-fill text-warning"></i>' : '<i class="bi bi-circle text-muted"></i>',
                    'action' => $this->clientTimesheetAction($row),
                ];
            }
        }
        return [
            'draw'              => 1,
            'recordsTotal'      => count($timesheets),
            'recordsFiltered'   => count($timesheets),
            'data'              => $array
        ];
    }

    private function clientTimesheetAction($timeSheetRow) {
        $action = '<a href="'.url('view-worker-details/'.$timeSheetRow['worker_details']['id']).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-2"><i class="fs-2 las la-arrow-right"></i></a>';

        $payrollWeekData = PayrollWeekDate::query()->select(['payroll_week_number', 'year', 'pay_date'])
            ->where( $timeSheetRow['job_details']['client_details']['payroll_week_starts'].'_payroll_end', '>=', $timeSheetRow['date'])
            ->first();

        $siteWeekLock = SiteWeekLock::query()->where('site_id', $timeSheetRow['job_details']['site_id'])
            ->where('payroll_week', $payrollWeekData['payroll_week_number'].'-'.$payrollWeekData['year'])
            ->first();

        $start_time = ($timeSheetRow['in_time'] != '00:00:00') ? date('H:i', strtotime($timeSheetRow['in_time'])) : '';
        $payDate = Carbon::parse($payrollWeekData['pay_date']);
        $action .= (!$siteWeekLock && $payDate->isFuture())
            ? '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" id="edit_timesheet" data-id="'.$timeSheetRow['id'].'" data-worker="'.$timeSheetRow['worker_details']['first_name'].' '.$timeSheetRow['worker_details']['middle_name'].' '.$timeSheetRow['worker_details']['last_name'].' ('.$timeSheetRow['date'].')" data-hours="'.number_format($timeSheetRow['hours_worked'], 2).'" data-start_time="'.$start_time.'"><i class="fs-2 las la-edit"></i></a>'
            : '';

        return $action;
    }

    public function updateClientPrimaryContact(Request $request){
        DB::beginTransaction();
        try {
            $clientId = $request->input('client_id');
            $primaryContactId = $request->input('primary_contact_id');

            $contact = ClientContact::query()->where('client_id', $clientId)->first();
            if (!$contact) {
                throw new \Exception('Client details not found, please try again later.');
            }

            ClientContact::query()->where('client_id', $clientId)
                ->update([
                    'primary_contact' => 0
                ]);

            ClientContact::query()->where('id', $primaryContactId)
                ->update([
                    'primary_contact' => 1
                ]);

            DB::commit();
            return self::responseWithSuccess('Primary contact details successfully set.');
        } catch (\Exception $e) {

            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateClientStatus(Request $request) {
        try {
            $params = $request->input();
            if ($params['status'] == 'Archived') {
                ClientJob::query()->where('client_id', $params['client_id'])->update([
                    'archived' => '1',
                ]);
                Client::query()->where('id', $params['client_id'])->delete();
            } else {
                Client::withTrashed()->where('id', $params['client_id'])->update([
                    'status' => $params['status'],
                    'deleted_at' => null
                ]);
            }
            return self::responseWithSuccess('Client status successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateClientPayDetails(Request $request){
        try {
            $params = $request->input();
            $client = Client::query()->where('id', $params['pay_details_update_id'])->first();
            if (!$client) {
                return self::responseWithError('Client details not found please try again.');
            }

            $rules = [
                'bonus_payment_margin' => 'required|numeric|min:0',
            ];

            if (!$client['payroll_week_starts']) {
                $rules['pay_roll_week_starts_on'] = 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday';
            }

            $validator = Validator::make($request->input(), $rules, [
                'pay_roll_week_starts_on.in' => 'Selected payroll start day is invalid.',
            ]);

            $message = $validator->errors()->messages();
            if ($message) {
                return self::validationError($message);
            }

            $updateArray = [
                'bonus_commission_percentage' => $params['bonus_payment_margin']
            ];

            if (!$client['payroll_week_starts']) {
                $updateArray['payroll_week_starts'] = $params['pay_roll_week_starts_on'];
            }

            Client::query()->where('id',$client['id'])->update($updateArray);

            ClientHelper::automaticClientActive($client['id']);
            return self::responseWithSuccess('Pay info successfully updated.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getJobWorkerAvailability(Request $request) {
        try {
            $params = $request->input();

            $job_id = $request->input('job_id');
            $requestWeekNumber = $params['wa_week_number'] ?? Carbon::now()->isoWeek;
            $requestYear = $params['wa_week_year'] ?? Carbon::now()->isoWeekYear;

            $carbon = Carbon::now()->setISODate($requestYear, $requestWeekNumber);
            switch ($params['week_type'] ?? '') {
                case 'next':
                    $carbon->addWeek();
                    break;

                case 'previous':
                    $carbon->subWeek();
                    break;
            }

            $week_number = $carbon->isoWeek;
            $week_year = $carbon->isoWeekYear;

            $payroll_start_date_column = $params['payroll_week_starts'].'_payroll_start';
            $payroll_end_date_column = $params['payroll_week_starts'].'_payroll_end';

            $pwData = PayrollWeekDate::query()->select(['id', 'payroll_week_number', $payroll_start_date_column, $payroll_end_date_column, 'pay_date'])
                ->where('payroll_week_number', $week_number)
                ->where('year', $week_year)
                ->first();

            if (!$pwData) {
                throw new \Exception('Payroll week number data not available.');
            }

            $startDate = Carbon::parse($pwData[$payroll_start_date_column]);

            $table_th = [];
            for ($i = 0; $i < 7; $i++) {
                $currentDate = $startDate->copy()->addDays($i)->format('Y-m-d');
                $jobShifts = JobShift::query()
                    ->where('job_id', $job_id)
                    ->whereDate('date', $currentDate)
                    ->first();

                if ($jobShifts) {
                    $th = '<a href="'.url('view-job-shift/'.$jobShifts['id']).'" target="_blank" class="text-gray-600">
                            <i class="fs-1 las la-calendar-day me-1"></i>'.$startDate->copy()->addDays($i)->format('D d').
                          '</a><br><span class="fs-5 fw-normal">'.Carbon::parse($jobShifts['start_time'])->format('Hi').'/'.$jobShifts['shift_length_hr'].'h'.$jobShifts['shift_length_min'].'m</span><br>';
                    $shiftTrueOrFalse = true;
                } else {
                    $th = '<i class="fs-1 las la-calendar-day me-1"></i>'.$startDate->copy()->addDays($i)->format('D d').'<br>
                           <a href="javascript:;" id="create_job_shift_th_btn" data-create_shift_date="'.$currentDate.'"><i class="fs-1 las la-plus-circle text-primary"></i></a>';
                    $shiftTrueOrFalse = false;
                }

                $table_th[] = [
                    'title' => $th,
                    'shiftTrueOrFalse' => $shiftTrueOrFalse,
                    'confirm' => 0,
                    'invited' => 0,
                    'available' => 0,
                ];
            }

            $query = ClientJobWorker::query()
                ->where('job_id', $job_id)
                ->whereNotNull('confirmed_at')
                ->whereNull('archived_at')
                ->whereNull('declined_at')
                ->with(['worker', 'rightsToWork', 'absence']);

            $clientJobWorker = $query->get()->toArray();
            $array  = [];
            if ($clientJobWorker) {
                foreach ($clientJobWorker as $row) {

                    $fullName = $row['worker']['first_name'] . ' ' . $row['worker']['middle_name'] . ' ' . $row['worker']['last_name'];
                    $dob = date('d/m/Y', strtotime($row['worker']['date_of_birth']));

                    $preparedRow['worker_detail'] = '<a href="'.url('view-worker-details/'.$row['worker']['id']).'" class="fw-bolder fs-6 p-2" target="_blank">' . $fullName . '</a><br><span class="fw-normal p-2">' . $dob . '</span>';
                    for ($i = 0; $i < 7; $i++) {
                        $shiftDate = $startDate->copy()->addDays($i)->format('Y-m-d');

                        $preparedRow['day_'.$i+1] = JobHelper::getWorkerAvailabilityBox([
                            'job_id' => $job_id,
                            'shift_date' => $shiftDate,
                            'worker_id' => $row['worker_id']
                        ], $table_th[$i]);
                    }
                    $array[] = $preparedRow;
                }
            }

            return [
                'draw'              => 1,
                'recordsTotal'      => count($clientJobWorker),
                'recordsFiltered'   => count($clientJobWorker),
                'data'              => $array,
                'worker_availability_tab_date' => Carbon::parse($pwData[$payroll_start_date_column])->format('D d M Y'),
                'table_th'          => $table_th,
                'wa_week_number'    => $week_number,
                'wa_week_year'      => $week_year,

            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function actionOnWorkerAvailability(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->input(), [
                'worker_availability_action_type' => 'required|string',
                'worker_availability_checked_worker' => 'required|array|min:1'
            ]);

            $message = $validator->errors()->messages();
            if ($message) {
                return self::validationError($message);
            }

            $actionType = $request->input('worker_availability_action_type');
            $checkedWorkers = $request->input('worker_availability_checked_worker');

            $validStatuses = [
                'add_to_shift_as_confirmed' => ['available', 'o/job', 'rest'],
                'invite_to_shift' => ['available', 'o/job'],
                'mark_invited_as_confirmed' => ['invited'],
                'mark_invited_as_declined' => ['invited'],
                'unassign_from_shift' => ['invited', 'confirmed'],
                'cancel_worker_from_shift' => ['invited', 'confirmed'],
                'mark_as_rest' => ['available'],
                'mark_as_sick' => ['available'],
            ];

            if (!isset($validStatuses[$actionType])) {
                throw new \Exception('Invalid action type selected.');
            }

            foreach ($checkedWorkers as $worker) {
                $validationParts = explode('_', $worker);

                if (count($validationParts) < 3) {
                    throw new \Exception('Invalid format. Expected: workerId_shiftId_status');
                }

                $normalizedStatus = strtolower(trim($validationParts[2]));
                if (!in_array($normalizedStatus, $validStatuses[$actionType])) {
                    $allowedStatuses = implode(' and ', $validStatuses[$actionType]);
                    throw new \Exception("This action applies to {$allowedStatuses} workers only.");
                }
            }

            if ($actionType == 'add_to_shift_as_confirmed') {
                foreach ($checkedWorkers as $worker) {
                    $parts = explode('_', $worker);
                    $shift = JobShift::query()->where('id', $parts[0])->first();
                    if (!$shift) {
                        throw new \Exception("Job Shift not found, SHIFT ID - {$parts[0]}.");
                    }
                    JobShiftWorker::query()->create([
                        'job_shift_id' => $shift['id'],
                        'worker_id'    => $parts[1],
                        'shift_date'   => $shift['date'],
                        'assign_type'  => 'Direct placement',
                        'confirmed_at' => Carbon::now(),
                        'start_time' => $shift['start_time']
                    ]);
                }
            } elseif ($actionType == 'invite_to_shift') {
                foreach ($checkedWorkers as $worker) {
                    $parts = explode('_', $worker);
                    $shift = JobShift::query()->where('id', $parts[0])->first();
                    if (!$shift) {
                        throw new \Exception("Job Shift not found, SHIFT ID - {$parts[0]}.");
                    }
                    JobShiftWorker::query()->create([
                        'job_shift_id' => $shift['id'],
                        'worker_id'    => $parts[1],
                        'shift_date'   => $shift['date'],
                        'assign_type'  => 'Invitation',
                        'confirmed_at' => null,
                        'start_time' => $shift['start_time']
                    ]);
                }
            } elseif ($actionType == 'mark_invited_as_confirmed') {
                foreach ($checkedWorkers as $worker) {
                    $parts = explode('_', $worker);
                    $shift = JobShift::query()->where('id', $parts[0])->first();
                    if (!$shift) {
                        throw new \Exception("Job Shift not found, SHIFT ID - {$parts[0]}.");
                    }
                    JobShiftWorker::query()->where('job_shift_id', $shift['id'])
                        ->where('worker_id', $parts[1])
                        ->update([
                            'confirmed_at' => Carbon::now(),
                            'last_updated_by' => Auth::id(),
                        ]);
                }
            } elseif ($actionType == 'mark_invited_as_declined') {
                foreach ($checkedWorkers as $worker) {
                    $parts = explode('_', $worker);
                    $shift = JobShift::query()->where('id', $parts[0])->first();
                    if (!$shift) {
                        throw new \Exception("Job Shift not found, SHIFT ID - {$parts[0]}.");
                    }
                    JobShiftWorker::query()->where('job_shift_id', $shift['id'])
                        ->where('worker_id', $parts[1])
                        ->update([
                            'declined_at' => Carbon::now(),
                            'cancelled_by' => 'Admin',
                            'cancelled_by_user_id' => Auth::id(),
                            'last_updated_by' => Auth::id(),
                        ]);
                }
            } elseif ($actionType == 'unassign_from_shift') {
                foreach ($checkedWorkers as $worker) {
                    $parts = explode('_', $worker);
                    $shift = JobShift::query()->where('id', $parts[0])->first();
                    if (!$shift) {
                        throw new \Exception("Job Shift not found, SHIFT ID - {$parts[0]}.");
                    }
                    JobShiftWorker::query()->where('job_shift_id', $shift['id'])
                        ->where('worker_id', $parts[1])
                        ->delete();
                }
            } elseif ($actionType == 'cancel_worker_from_shift') {
                foreach ($checkedWorkers as $worker) {
                    $parts = explode('_', $worker);
                    $shift = JobShift::query()->where('id', $parts[0])->first();
                    if (!$shift) {
                        throw new \Exception("Job Shift not found, SHIFT ID - {$parts[0]}.");
                    }
                    JobShiftWorker::query()->where('job_shift_id', $shift['id'])
                        ->where('worker_id', $parts[1])
                        ->update([
                            'cancelled_at' => Carbon::now(),
                            'cancelled_by' => 'Admin',
                            'cancelled_by_user_id' => Auth::id(),
                            'last_updated_by' => Auth::id(),
                        ]);
                }
            } else {
                $absence_type = ($actionType == 'mark_as_rest') ? 'Rest' : 'Sickness';
                foreach ($checkedWorkers as $worker) {
                    $parts = explode('_', $worker);
                    $shift = JobShift::query()->where('id', $parts[0])->first();
                    if (!$shift) {
                        throw new \Exception("Job Shift not found, SHIFT ID - {$parts[0]}.");
                    }
                    $offDate = date('Y-m-d', strtotime($shift['date']));
                    Absence::query()->create([
                        'worker_id'     => $parts[1],
                        'absence_type'  => $absence_type,
                        'start_date'    => $offDate,
                        'end_date'      => $offDate,
                        'added_by'      => Auth::id(),
                    ]);
                }
            }

            DB::commit();
            return self::responseWithSuccess('Your action has been completed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function unArchiveClientJobWorker($id){
        try {
            $ClientJobWorker = ClientJobWorker::query()->where('id', $id)->first();
            if (!$ClientJobWorker) {
                return self::responseWithError('Client job worker details not found, please try again later.');
            }
            if(!$ClientJobWorker['archived_at']){
                return self::responseWithError('This associate already linked to this job.');
            }
            ClientJobWorker::query()->where('id', $id)
                ->update([
                'archived_at' => null
            ]);

            return self::responseWithSuccess('Associate successfully relinked.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
