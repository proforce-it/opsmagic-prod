<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Bonus\Bonus;
use App\Models\Client\Client;
use App\Models\Client\ClientJob;
use App\Models\Client\Site;
use App\Models\Group\CostCentre;
use App\Models\Job\PayrollLineItem;
use App\Models\Payroll\PayrollWeekDate;
use App\Models\Timesheet\Timesheet;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    use JsonResponse;
    public function financialReport() {
        $payroll_week_number = PayrollWeekDate::query()->get();
        $client = Client::query()->select('id', 'company_name')->get();
        $site = Site::query()->where('archived',  '0')->with('client_details')->get();
        $job = ClientJob::query()->where('archived', '0')->with(['site_details', 'client_details'])->get();
        $worker = Worker::query()->select('id', 'worker_no', 'first_name', 'middle_name', 'last_name')->where('status', 'Active')->get();
        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('reports.financial.financial_report', compact('payroll_week_number', 'client', 'site', 'job', 'worker', 'costCentre'));
    }

    public function getFinancialSiteSummaryReport(Request $request) {
        try {

            if (!request('params')['payroll_week_number'])
                throw new \Exception('Please select payroll week number.');

            $payroll_week = str_replace('_', '-', request('params')['payroll_week_number']);
            $pwdNode = explode('_', request('params')['payroll_week_number']);
            $pwData = PayrollWeekDate::query()->where('payroll_week_number', $pwdNode[0])
                ->where('year', $pwdNode[1])
                ->first();

            if (!$pwData)
                throw new \Exception('Payment week data not found.');

            $site = Site::query()
                ->when(request('params')['cost_center'] != 'All' && request('params')['cost_center'] != '', function ($q) { return $q->where('cost_center', request('params')['cost_center']); })
                ->when(request('params')['client'] != 'All' && request('params')['client'] != '', function ($q) { return $q->where('client_id', request('params')['client']); })
                ->when(request('params')['site'] != 'All' && request('params')['site'] != '', function ($q) { return $q->where('id', request('params')['site']); })
                ->with(['payroll_line_item_details' => function($query) use($payroll_week) {
                    $query->where('payroll_week', $payroll_week)
                        ->when(request('params')['job'] != '', function ($q) { return $q->where('job_id', request('params')['job']); })
                        ->when(request('params')['worker'] != 'All' && request('params')['worker'] != '', function ($q) { return $q->where('worker_id', request('params')['worker']); });
                }, 'client_details', 'job_details'])
                ->get()
                ->toArray();

            $array = [];
            if ($site) {
                foreach ($site as $row) {

                    $payroll_start_date_column = $row['client_details']['payroll_week_starts'].'_payroll_start';
                    $payroll_end_date_column = $row['client_details']['payroll_week_starts'].'_payroll_end';

                    $jobIds = (request('params')['job'] != null) ? [request('params')['job']] : array_column($row['job_details'], 'id');
                    $timesheetData = Timesheet::query()->whereIn('job_id', $jobIds)
                        ->whereBetween('date', [$pwData[$payroll_start_date_column], $pwData[$payroll_end_date_column]])
                        ->when(request('params')['worker'] != 'All' && request('params')['worker'] != '', function ($q) {
                            return $q->where('worker_id', request('params')['worker']);
                        })
                        ->count();

                    $bonus_pay = array_sum(array_column(array_filter($row['payroll_line_item_details'], function($item) {
                        return $item['pay_rate_name'] === 'Bonus'; }
                    ), 'total_pay'));
                    $bonus_charge = array_sum(array_column(array_filter($row['payroll_line_item_details'], function($item) {
                        return $item['pay_rate_name'] === 'Bonus'; }
                    ), 'total_charge'));

                    $pay = array_sum(array_column(array_filter($row['payroll_line_item_details'], function($item) {
                        return $item['pay_rate_name'] !== 'Bonus'; }
                    ), 'total_pay'));
                    $charge = array_sum(array_column(array_filter($row['payroll_line_item_details'], function($item) {
                        return $item['pay_rate_name'] !== 'Bonus'; }
                    ), 'total_charge'));

                    $hour = array_sum(array_column($row['payroll_line_item_details'], 'total_hours'));
                    $total_pay = $bonus_pay + $pay;
                    $total_charge = $bonus_charge + $charge;

                    if ( $timesheetData != 0 && $hour != 0 && $bonus_pay != 0 && $bonus_charge != 0 && $pay != 0 && $charge != 0 && $total_pay != 0 && $total_charge != 0 ) {
                        $array[] = [
                            'site_name' => $row['site_name'],
                            'shifts' => $timesheetData,

                            'hour' => number_format($hour, 2),
                            'bonus_pay' => number_format($bonus_pay, 2),
                            'bonus_charge' => number_format($bonus_charge, 2),
                            'pay' => number_format($pay, 2),
                            'charge' => number_format($charge, 2),
                            'total_pay' => number_format($total_pay, 2),
                            'total_charge' => number_format($total_charge, 2),

                            'hour_c' => $hour,
                            'bonus_pay_c' => $bonus_pay,
                            'bonus_charge_c' => $bonus_charge,
                            'pay_c' => $pay,
                            'charge_c' => $charge,
                            'total_pay_c' => $total_pay,
                            'total_charge_c' => $total_charge,
                        ];
                    }
                }
            }


        } catch (\Exception $e) {
            $array = [];
        }

        return [
            'draw'              => 1,
            'recordsTotal'      => count($array),
            'recordsFiltered'   => count($array),
            'data'              => $array,
            'total_shift'       => array_sum(array_column($array, 'shifts')),
            'total_hour'        => number_format(array_sum(array_column($array, 'hour_c')), 2),
            'bonus_pay'         => number_format(array_sum(array_column($array, 'bonus_pay_c')), 2),
            'bonus_charge'      => number_format(array_sum(array_column($array, 'bonus_charge_c')), 2),
            'pay'               => number_format(array_sum(array_column($array, 'pay_c')), 2),
            'charge'            => number_format(array_sum(array_column($array, 'charge_c')), 2),
            'total_pay'         => number_format(array_sum(array_column($array, 'total_pay_c')), 2),
            'total_charge'      => number_format(array_sum(array_column($array, 'total_charge_c')), 2),
        ];
    }

    public function getFinancialJobSummaryReport(Request $request) {
        try {
            if (!request('params')['payroll_week_number'])
                throw new \Exception('Please select payroll week number.');

            $payroll_week = str_replace('_', '-', request('params')['payroll_week_number']);
            $pwdNode = explode('_', request('params')['payroll_week_number']);
            $pwData = PayrollWeekDate::query()->where('payroll_week_number', $pwdNode[0])
                ->where('year', $pwdNode[1])
                ->first();

            if (!$pwData)
                throw new \Exception('Payment week data not found.');

            $job = ClientJob::query()
                ->when(request('params')['client'] != 'All' && request('params')['client'] != '', function ($q) { return $q->where('client_id', request('params')['client']); })
                ->when(request('params')['site'] != 'All' && request('params')['site'] != '', function ($q) { return $q->where('site_id', request('params')['site']); })
                ->when(request('params')['job'] != '', function ($q) { return $q->where('id', request('params')['job']); })
                ->whereHas('site_details', function($query) {
                    $query->when(
                        request('params')['cost_center'] != 'All' && request('params')['cost_center'] != '',
                        function ($q) {
                            return $q->where('cost_center', request('params')['cost_center']);
                        }
                    );
                })
                ->with(['site_details', 'client_details', 'payroll_line_item_details' => function($query) use($payroll_week) {
                    $query->where('payroll_week', $payroll_week)
                        ->when(request('params')['worker'] != 'All' && request('params')['worker'] != '', function ($q) { return $q->where('worker_id', request('params')['worker']); });
                }])
                ->get()
                ->toArray();

            $array = [];
            if ($job) {
                foreach ($job as $row) {

                    $payroll_start_date_column = $row['client_details']['payroll_week_starts'].'_payroll_start';
                    $payroll_end_date_column = $row['client_details']['payroll_week_starts'].'_payroll_end';

                    $jobIds = (request('params')['job'] != '') ? request('params')['job'] : $row['id'];
                    $timesheetData = Timesheet::query()->where('job_id', $jobIds)
                        ->whereBetween('date', [$pwData[$payroll_start_date_column], $pwData[$payroll_end_date_column]])
                        ->when(request('params')['worker'] != 'All' && request('params')['worker'] != '', function ($q) {
                            return $q->where('worker_id', request('params')['worker']);
                        })
                        ->count();

                    $bonus_pay = array_sum(array_column(array_filter($row['payroll_line_item_details'], function($item) {
                        return $item['pay_rate_name'] === 'Bonus'; }
                    ), 'total_pay'));
                    $bonus_charge = array_sum(array_column(array_filter($row['payroll_line_item_details'], function($item) {
                        return $item['pay_rate_name'] === 'Bonus'; }
                    ), 'total_charge'));

                    $pay = array_sum(array_column(array_filter($row['payroll_line_item_details'], function($item) {
                        return $item['pay_rate_name'] !== 'Bonus'; }
                    ), 'total_pay'));
                    $charge = array_sum(array_column(array_filter($row['payroll_line_item_details'], function($item) {
                        return $item['pay_rate_name'] !== 'Bonus'; }
                    ), 'total_charge'));

                    $array[] = [
                        'site_name' => $row['site_details']['site_name'],
                        'job_name' => $row['name'],

                        'shifts' => $timesheetData,

                        'hour' => number_format(array_sum(array_column($row['payroll_line_item_details'], 'total_hours')),2),
                        'bonus_pay' => number_format($bonus_pay,2),
                        'bonus_charge' => number_format($bonus_charge, 2),
                        'pay' => number_format($pay, 2),
                        'charge' => number_format($charge, 2),
                        'total_pay' => number_format(($bonus_pay + $pay), 2),
                        'total_charge' => number_format(($bonus_charge + $charge), 2),

                        'hour_c' => array_sum(array_column($row['payroll_line_item_details'], 'total_hours')),
                        'bonus_pay_c' => $bonus_pay,
                        'bonus_charge_c' => $bonus_charge,
                        'pay_c' => $pay,
                        'charge_c' => $charge,
                        'total_pay_c' => $bonus_pay + $pay,
                        'total_charge_c' => $bonus_charge + $charge,
                    ];
                }
            }


        } catch (\Exception $e) {
            $array = [];
        }

        return [
            'draw'              => 1,
            'recordsTotal'      => count($array),
            'recordsFiltered'   => count($array),
            'data'              => $array,
            'total_shift'       => array_sum(array_column($array, 'shifts')),
            'total_hour'        => number_format(array_sum(array_column($array, 'hour_c')), 2),
            'bonus_pay'         => number_format(array_sum(array_column($array, 'bonus_pay_c')), 2),
            'bonus_charge'      => number_format(array_sum(array_column($array, 'bonus_charge_c')), 2),
            'pay'               => number_format(array_sum(array_column($array, 'pay_c')), 2),
            'charge'            => number_format(array_sum(array_column($array, 'charge_c')), 2),
            'total_pay'         => number_format(array_sum(array_column($array, 'total_pay_c')), 2),
            'total_charge'      => number_format(array_sum(array_column($array, 'total_charge_c')), 2),
        ];
    }

    public function getFinancialWorkerSummaryReport(Request $request) {
        try {
            if (!request('params')['payroll_week_number'])
                throw new \Exception('Please select payroll week number.');

            $payroll_week = str_replace('_', '-', request('params')['payroll_week_number']);
            $pwdNode = explode('_', request('params')['payroll_week_number']);
            $pwData = PayrollWeekDate::query()->where('payroll_week_number', $pwdNode[0])
                ->where('year', $pwdNode[1])
                ->first();

            if (!$pwData)
                throw new \Exception('Payment week data not found.');

            $workerSummary = PayrollLineItem::query()
                ->where('payroll_week', $payroll_week)
                ->when(request('params')['site'] != 'All' && request('params')['site'] != '', function ($q) { return $q->where('site_id', request('params')['site']); })
                ->when(request('params')['job'] != '', function ($q) { return $q->where('job_id', request('params')['job']); })
                ->when(request('params')['worker'] != 'All' && request('params')['worker'] != '', function ($q) { return $q->where('worker_id', request('params')['worker']); })
                ->whereHas('site_details', function($query) {
                    $query->when(request('params')['cost_center'] != 'All' && request('params')['cost_center'] != '',
                        function ($q) { return $q->where('cost_center', request('params')['cost_center']); }
                    );
                })
                ->whereHas('job_details.client_details', function($query) {
                    $query->when(request('params')['client'] != 'All' && request('params')['client'] != '',
                        function ($q) { return $q->where('id', request('params')['client']); }
                    );
                })
                ->with(['site_details', 'worker_details', 'job_details'])
                ->get()
                ->groupBy(function ($lineItem) {
                    return $lineItem->site_id . '-' . $lineItem->worker_id . '-' . $lineItem->job_id;
                })
                ->map(function ($items) use ($pwData) {

                    $basicPayWhere = $items->where('pay_rate_name', 'Basic pay');
                    $overtimeWhere = $items->where('pay_rate_name', 'Overtime');
                    $bonusWhere = $items->where('pay_rate_name', 'Bonus');

                    $total_hours = $basicPayWhere->sum('total_hours') + $overtimeWhere->sum('total_hours');

                    $bonus_pay = $bonusWhere->sum('total_pay');
                    $bonus_charge = $bonusWhere->sum('total_charge');

                    $pay = $basicPayWhere->sum('total_pay') + $overtimeWhere->sum('total_pay');
                    $charge = $basicPayWhere->sum('total_charge') + $overtimeWhere->sum('total_charge');

                    $total_pay = $bonus_pay + $pay;
                    $total_charge = $bonus_charge + $charge;

                    $items = $items->first();
                    $payroll_start_date_column = $items['job_details']['client_details']['payroll_week_starts'].'_payroll_start';
                    $payroll_end_date_column = $items['job_details']['client_details']['payroll_week_starts'].'_payroll_end';

                    $jobIds = (request('params')['job'] != '') ? request('params')['job'] : $items['job_id'];
                    $timesheetData = Timesheet::query()
                        ->where('job_id', $jobIds)
                        ->where('worker_id', $items['worker_id'])
                        ->whereBetween('date', [$pwData[$payroll_start_date_column], $pwData[$payroll_end_date_column]])
                        ->count();

                    return [
                        'site_name' => $items['site_details']['site_name'],
                        'job_name' => $items['job_details']['name'],
                        'worker_name' => $items['worker_details']['first_name'].' '.$items['worker_details']['middle_name'].' '.$items['worker_details']['last_name'],

                        'shifts' => $timesheetData,
                        'hour' => number_format($total_hours,2),
                        'bonus_pay' => number_format($bonus_pay, 2),
                        'bonus_charge' => number_format($bonus_charge, 2),
                        'pay' => number_format($pay,2),
                        'charge' => number_format($charge,2),
                        'total_pay' => number_format($total_pay,2),
                        'total_charge' => number_format($total_charge,2),

                        'hour_c' => $total_hours,
                        'bonus_pay_c' => $bonus_pay,
                        'bonus_charge_c' => $bonus_charge,
                        'pay_c' => $pay,
                        'charge_c' => $charge,
                        'total_pay_c' => $total_pay,
                        'total_charge_c' => $total_charge,
                    ];
                })
                ->toArray();

            $workerSummary = array_values($workerSummary);
        } catch (\Exception $e) {
            $workerSummary = [];
        }

        return [
            'draw'              => 1,
            'recordsTotal'      => count($workerSummary),
            'recordsFiltered'   => count($workerSummary),
            'data'              => $workerSummary,
            'total_shift'       => array_sum(array_column($workerSummary, 'shifts')),
            'total_hour'        => number_format(array_sum(array_column($workerSummary, 'hour_c')), 2),
            'bonus_pay'         => number_format(array_sum(array_column($workerSummary, 'bonus_pay_c')), 2),
            'bonus_charge'      => number_format(array_sum(array_column($workerSummary, 'bonus_charge_c')), 2),
            'pay'               => number_format(array_sum(array_column($workerSummary, 'pay_c')), 2),
            'charge'            => number_format(array_sum(array_column($workerSummary, 'charge_c')), 2),
            'total_pay'         => number_format(array_sum(array_column($workerSummary, 'total_pay_c')), 2),
            'total_charge'      => number_format(array_sum(array_column($workerSummary, 'total_charge_c')), 2),
        ];
    }

    public function getFinancialPayrollSummaryReport(Request $request) {
        try {
            if (!request('params')['payroll_week_number'])
                throw new \Exception('Please select payroll week number.');

            $payroll_week = str_replace('_', '-', request('params')['payroll_week_number']);
            $pwdNode = explode('_', request('params')['payroll_week_number']);
            $pwData = PayrollWeekDate::query()->where('payroll_week_number', $pwdNode[0])
                ->where('year', $pwdNode[1])
                ->first();

            if (!$pwData)
                throw new \Exception('Payment week data not found.');

            $payrollSummary = PayrollLineItem::query()
                ->whereNot('pay_rate_name', 'Bonus')
                ->where('payroll_week', $payroll_week)
                ->when(request('params')['site'] != 'All' && request('params')['site'] != '', function ($q) { return $q->where('site_id', request('params')['site']); })
                ->when(request('params')['job'] != '', function ($q) { return $q->where('job_id', request('params')['job']); })
                ->when(request('params')['worker'] != 'All' && request('params')['worker'] != '', function ($q) { return $q->where('worker_id', request('params')['worker']); })
                ->whereHas('site_details', function($query) {
                    $query->when(request('params')['cost_center'] != 'All' && request('params')['cost_center'] != '',
                        function ($q) { return $q->where('cost_center', request('params')['cost_center']); }
                    );
                })
                ->whereHas('job_details.client_details', function($query) {
                    $query->when(request('params')['client'] != 'All' && request('params')['client'] != '',
                        function ($q) { return $q->where('id', request('params')['client']); }
                    );
                })
                ->with(['site_details', 'worker_details', 'job_details'])
                ->get()
                ->toArray();

            $array = [];
            if($payrollSummary) {
                foreach ($payrollSummary as $row) {
                    $payroll_start_date_column = $row['job_details']['client_details']['payroll_week_starts'].'_payroll_start';
                    $payroll_end_date_column = $row['job_details']['client_details']['payroll_week_starts'].'_payroll_end';

                    $startDate = Carbon::parse($pwData[$payroll_start_date_column]);
                    $endDate = Carbon::parse($pwData[$payroll_end_date_column]);

                    $currentDate = $startDate;
                    $dayIndex = 1;
                    while ($currentDate <= $endDate) {
                        $hour = $row['day_' . $dayIndex . '_hours'];
                        if ($hour != 0) {
                            $total_pay = $hour * $row['pay_rate'];
                            $total_charge = $hour * $row['charge_rate'];

                            $formattedDate = $currentDate->format('d/m/Y') . ' - ' . $currentDate->format('D');
                            $array[] = [
                                'date' => $formattedDate,
                                'site_name' => $row['site_details']['site_name'],
                                'job_name' => $row['job_details']['name'],
                                'worker_name' => $row['worker_details']['first_name'].' '.$row['worker_details']['middle_name'].' '.$row['worker_details']['last_name'],
                                'pay_rate_name' => ($row['pay_rate_name'] == 'Basic pay') ? 'Basic' : 'Overtime',
                                'hour' => $hour,
                                'pay' => number_format($total_pay, 2),
                                'charge' => number_format($total_charge, 2),
                                'hour_c' => $hour,
                                'pay_c' => $total_pay,
                                'charge_c' => $total_charge,
                            ];
                        }
                        $dayIndex++;
                        $currentDate->addDay();
                    }
                }
            }

        } catch (\Exception $e) {
            $array = [];
        }

        return [
            'draw'              => 1,
            'recordsTotal'      => count($array),
            'recordsFiltered'   => count($array),
            'data'              => $array,
            'total_hour'        => number_format(array_sum(array_column($array, 'hour_c')), 2),
            'total_pay'         => number_format(array_sum(array_column($array, 'pay_c')), 2),
            'total_charge'      => number_format(array_sum(array_column($array, 'charge_c')), 2),
        ];
    }
}
