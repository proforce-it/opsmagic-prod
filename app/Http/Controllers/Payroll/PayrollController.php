<?php

namespace App\Http\Controllers\Payroll;

use App\Exports\PayrollLineItemExport;
use App\Http\Controllers\Bonus\BonusUploaderController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Timesheet\TimeSheetUploaderController;
use App\Models\Bonus\Bonus;
use App\Models\Client\Client;
use App\Models\Client\Site;
use App\Models\Client\SiteWeekLock;
use App\Models\Job\ClientJobPayRate;
use App\Models\Job\ExtraPayRateDay;
use App\Models\Job\ExtraPayRateMap;
use App\Models\Job\PayrollLineItem;
use App\Models\Payroll\PayrollWeekDate;
use App\Models\Timesheet\Timesheet;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    use JsonResponse;
    public function createPayrollAction(Request $request) {
        $params = $request->input();

        DB::beginTransaction();
        try {

            /*--- Generated entries delete ---*/
            $this->deleteReportAction($params);

            /* --- Get site ---*/
            $site = Site::query()->where('id', $params['site_id'])->with(['job_details', 'client_details'])->first()->toArray();
            if (!$site)
                throw new \Exception('Site data not found, please try again later');

            if (!$site['job_details'])
                throw new \Exception('Job data not found, please try again later');

            if (!$site['client_details'])
                throw new \Exception('Client data not found, please try again later');

            /* --- Get Payroll week data ---*/
            $payroll_start_date_column = $site['client_details']['payroll_week_starts'].'_payroll_start';
            $payroll_end_date_column = $site['client_details']['payroll_week_starts'].'_payroll_end';

            $payroll_week = str_replace('_', '-', $params['pwd']);
            $pwdNode = explode('_', $params['pwd']);
            $pwData = PayrollWeekDate::query()->select(['id', 'payroll_week_number', $payroll_start_date_column, $payroll_end_date_column, 'pay_date'])
                ->where('payroll_week_number', $pwdNode[0])
                ->where('year', $pwdNode[1])
                ->first();

            if (!$pwData)
                throw new \Exception('Payroll week data not found, please try again later');

            /*--- Date array ---*/
            $payrollStartDate = Carbon::parse($pwData[$payroll_start_date_column]);
            $payrollEndDate = Carbon::parse($pwData[$payroll_end_date_column]);

            $dateArray = [];
            while ($payrollStartDate->lte($payrollEndDate)) {
                $dateArray[] = $payrollStartDate->toDateString();
                $payrollStartDate->addDay();
            }

            $organizedData = [];
            $basicPayTotalHoursArrayForPayRateMap = [];
            foreach ($dateArray as $index => $date) {
                foreach ($site['job_details'] as $job_row) {
                    $job_row['pay_rate_details'] = ClientJobPayRate::query()->where('job_id', $job_row['id'])
                        ->where('pay_rate_valid_from', '<=', $date)
                        ->where(function($query) use ($date) {
                            $query->where('pay_rate_valid_to', '>=', $date)
                                ->orWhereNull('pay_rate_valid_to');
                        })
                        ->whereIn('status', ['C', 'P'])
                        ->first();

                    if (!$job_row['pay_rate_details']) {
                        continue;
                    }

                    $pay_rate_error_messages = [];
                    if (empty($job_row['pay_rate_details']['default_overtime_pay_rate'])) {
                        $pay_rate_error_messages[] = 'Overtime pay rate';
                    }

                    if (empty($job_row['pay_rate_details']['default_overtime_charge_rate'])) {
                        $pay_rate_error_messages[] = 'Overtime charge rate';
                    }

                    if (empty($job_row['pay_rate_details']['default_overtime_hours_threshold'])) {
                        $pay_rate_error_messages[] = 'Overtime paid after';
                    }

                    if (count($pay_rate_error_messages) > 0 && count($pay_rate_error_messages) != 3) {
                        throw new \Exception(implode(' and ', $pay_rate_error_messages).' is not define in '.$job_row['name'].' job');
                    }

                    $timesheetData = Timesheet::query()
                        ->where('job_id', $job_row['id'])
                        ->whereBetween('date', [$pwData[$payroll_start_date_column], $pwData[$payroll_end_date_column]])
                        ->with(['worker_details', 'job_details'])
                        ->get()
                        ->toArray();

                    $dayData = array_filter($timesheetData, function($item) use ($date) {
                        return $item['date'] == $date;
                    });

                    /*--- BEGIN LOGIC TIMESHEET HOURS & OT ---*/
                    if ($job_row['pay_rate_type'] == 'flat_rate') {
                        $organizedData = $this->calculateFlatPayRateWise($dayData, $site, $job_row, $payroll_week, $index, $organizedData);
                    } else {
                        $payRateMapOrganizedData = $this->calculatePayRateMapWise($dayData, $site, $job_row, $payroll_week, $index, $organizedData, $basicPayTotalHoursArrayForPayRateMap);
                        $organizedData = $payRateMapOrganizedData['organizedData'];
                        $basicPayTotalHoursArrayForPayRateMap = $payRateMapOrganizedData['basicPayTotalHoursArrayForPayRateMap'];
                    }
                    /*--- END LOGIC TIMESHEET HOURS & OT ---*/

                    /*--- BEGIN CALCULATE BONUS ---*/
                    $organizedData = $this->calculateBonus($job_row, $pwdNode, $site, $payroll_week, $organizedData);
                    /*--- END CALCULATE BONUS ---*/
                }
            }

            if (!$organizedData)
                throw new \Exception('Timesheet and bonus entry not available in this week');

            $payrollLineItemArray = Arr::sort(array_values($organizedData), function ($value) {
                $payRatePriority = [
                    'Basic pay' => 1,
                    'Overtime' => 2,
                    'Bonus' => 3
                ];

                return [
                    $value['worker_id'],
                    $payRatePriority[$value['pay_rate_name']] ?? 4,
                ];
            });

            SiteWeekLock::query()->create([
                'site_id' => $site['id'],
                'payroll_week' => $payroll_week
            ]);
            PayrollLineItem::query()->insert(array_values($payrollLineItemArray));
            Timesheet::query()->whereIn('job_id', array_column($site['job_details'], 'id'))
                ->whereBetween('date', [$pwData[$payroll_start_date_column], $pwData[$payroll_end_date_column]])
                ->update([
                    'locked_at' => Carbon::now()
                ]);
            Bonus::query()->whereIn('job_id', array_column($site['job_details'], 'id'))
                ->where('week_number', $pwdNode[0])
                ->where('week_year_number', $pwdNode[1])
                ->update([
                    'locked_at' => Carbon::now()
                ]);

            DB::commit();
            return self::responseWithSuccess('Payroll report successfully created', [
                'client_name'       => $site['client_details']['company_name'],
                'site_name'         => $site['site_name'],
                'payroll_week_number'=> $payroll_week,
                'pay_date'          => date('d/m/Y', strtotime($pwData['pay_date'])),
                'client_week_start' => $site['client_details']['payroll_week_starts'],
                'date_between'      => date('d/m/Y', strtotime($pwData[$payroll_start_date_column])).' - '. date('d/m/Y', strtotime($pwData[$payroll_end_date_column])),
                'total_charge'      => number_format(array_sum(array_column($payrollLineItemArray, 'total_charge')), 2),
                'total_pay'         => number_format(array_sum(array_column($payrollLineItemArray, 'total_pay')), 2),
                'view_report_url'   => url('view-payroll-report?payroll='.$site['client_details']['id'].'.'.$site['id'].'.'.$params['pwd'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responseWithError('Error - '.$e->getMessage());
        }
    }

    private function calculateFlatPayRateWise($dayData, $site, $job_row, $payroll_week, $index, $organizedData) {
        $overtime_hours_threshold = $job_row['pay_rate_details']['default_overtime_hours_threshold'] ?? 0;
        $overtime_type = $job_row['pay_rate_details']['overtime_type'];

        foreach ($dayData as $entry) {
            $organizedDataIndex = $job_row['id'] . '_' . $entry['worker_id'] . '_basic_pay';

            if (!isset($organizedData[$organizedDataIndex])) {
                $organizedData[$organizedDataIndex] = $this->generatePayRollReportArray(
                    $site['id'],
                    $job_row['id'],
                    $entry['worker_id'],
                    $entry['worker_details']['payroll_reference'],
                    $payroll_week,
                    'Basic pay',
                    'flat_rate',
                    $job_row['pay_rate_details']['base_charge_rate'] ?? 0,
                    $job_row['pay_rate_details']['base_pay_rate'] ?? 0
                );
            }

            $dayIndex = $index + 1;
            $dayColumn = 'day_' . $dayIndex . '_hours';

            $hoursWorked = (float) $entry['hours_worked'];
            $regularHours = $hoursWorked;
            $perDayOvertimeHours = 0;

            if ($overtime_type === 'hours_per_day' && $overtime_hours_threshold > 0) {
                if ($hoursWorked > $overtime_hours_threshold) {
                    $regularHours = $overtime_hours_threshold;
                    $perDayOvertimeHours = $hoursWorked - $overtime_hours_threshold;
                }
            }

            $organizedData[$organizedDataIndex][$dayColumn] += $regularHours;
            $organizedData[$organizedDataIndex]['total_hours'] += $regularHours;
            /*$organizedData[$organizedDataIndex]['total_charge'] = $organizedData[$organizedDataIndex]['total_hours'] * $job_row['pay_rate_details']['base_charge_rate'];
            $organizedData[$organizedDataIndex]['total_pay'] = $organizedData[$organizedDataIndex]['total_hours'] * $job_row['pay_rate_details']['base_pay_rate'];*/
            $organizedData[$organizedDataIndex]['total_charge'] += $regularHours * $job_row['pay_rate_details']['base_charge_rate'];
            $organizedData[$organizedDataIndex]['total_pay'] += $regularHours * $job_row['pay_rate_details']['base_pay_rate'];

            if ($perDayOvertimeHours > 0) {
                $overtimeIndex = $job_row['id'] . '_' . $entry['worker_id'] . '_overtime';

                if (!isset($organizedData[$overtimeIndex])) {
                    $organizedData[$overtimeIndex] = $this->generatePayRollReportArray(
                        $site['id'],
                        $job_row['id'],
                        $entry['worker_id'],
                        $entry['worker_details']['payroll_reference'],
                        $payroll_week,
                        'Overtime',
                        'flat_rate',
                        $job_row['pay_rate_details']['default_overtime_charge_rate'] ?? 0,
                        $job_row['pay_rate_details']['default_overtime_pay_rate'] ?? 0
                    );
                }

                $organizedData[$overtimeIndex][$dayColumn] += $perDayOvertimeHours;
                $organizedData[$overtimeIndex]['total_hours'] += $perDayOvertimeHours;
                $organizedData[$overtimeIndex]['total_charge'] += $perDayOvertimeHours * $job_row['pay_rate_details']['default_overtime_charge_rate'];
                $organizedData[$overtimeIndex]['total_pay'] += $perDayOvertimeHours * $job_row['pay_rate_details']['default_overtime_pay_rate'];
            }

            if ($overtime_type === 'hours_per_week' && $overtime_hours_threshold > 0) {
                $overtime_hours = $organizedData[$organizedDataIndex]['total_hours'] - $overtime_hours_threshold;
                if ($overtime_hours > 0) {
                    $overtimeIndex = $job_row['id'] . '_' . $entry['worker_id'] . '_overtime';

                    if (!isset($organizedData[$overtimeIndex])) {
                        $organizedData[$overtimeIndex] = $this->generatePayRollReportArray(
                            $site['id'],
                            $job_row['id'],
                            $entry['worker_id'],
                            $entry['worker_details']['payroll_reference'],
                            $payroll_week,
                            'Overtime',
                            'flat_rate',
                            $job_row['pay_rate_details']['default_overtime_charge_rate'] ?? 0,
                            $job_row['pay_rate_details']['default_overtime_pay_rate'] ?? 0
                        );
                    }

                    $organizedData[$organizedDataIndex][$dayColumn] -= $overtime_hours;
                    $organizedData[$organizedDataIndex]['total_hours'] -= $overtime_hours;
                    $organizedData[$organizedDataIndex]['total_charge'] -= $overtime_hours * $job_row['pay_rate_details']['base_charge_rate'];
                    $organizedData[$organizedDataIndex]['total_pay'] -= $overtime_hours * $job_row['pay_rate_details']['base_pay_rate'];

                    $organizedData[$overtimeIndex][$dayColumn] += $overtime_hours;
                    $organizedData[$overtimeIndex]['total_hours'] += $overtime_hours;
                    $organizedData[$overtimeIndex]['total_charge'] += $overtime_hours * $job_row['pay_rate_details']['default_overtime_charge_rate'];
                    $organizedData[$overtimeIndex]['total_pay'] += $overtime_hours * $job_row['pay_rate_details']['default_overtime_pay_rate'];
                }
            }
        }
        return $organizedData;
    }

    private function calculatePayRateMapWise($dayData, $site, $job_row, $payroll_week, $index, $organizedData, $basicPayTotalHoursArrayForPayRateMap) {
        $overtime_type = $job_row['pay_rate_details']['overtime_type'];
        $overtime_hours_threshold = $job_row['pay_rate_details']['default_overtime_hours_threshold'] ?? 0;

        foreach ($dayData as $entry) {

            /*--- BEGIN used only for weekly overtime calculation ---*/
            $basicPayCountIndex = $job_row['id'] . '_' . $entry['worker_id'] . '_basic_pay';
            if (!isset($basicPayTotalHoursArrayForPayRateMap[$basicPayCountIndex])) {
                $basicPayTotalHoursArrayForPayRateMap[$basicPayCountIndex] = [
                    'total_hours' => 0
                ];
            }
            /*--- END used only for weekly overtime calculation ---*/

            $dayIndex = $index + 1;
            $dayColumn = 'day_' . $dayIndex . '_hours';
            $dailyWorkingHours = 0;
            $perDayOvertimeHours = 0;

            $date = $entry['date'];
            $in_time = $entry['in_time'];
            $out_time = $entry['out_time'];
            $startDateAndTime = Carbon::parse("$date $in_time");
            $endDateAndTime = Carbon::parse("$date $out_time");

            if ($endDateAndTime->lt($startDateAndTime)) {
                $endDateAndTime->addDay();
            }

            $extraPayDay = ExtraPayRateDay::query()->where('default_pay_rate_id', $job_row['pay_rate_details']['id'])
                ->where('day', $startDateAndTime->format('l'))
                ->first();

            $current = $startDateAndTime->copy();

            while ($current < $endDateAndTime) {
                $timeSlotKey = $current->format('Hi');
                $next = $current->copy()->addMinutes(30);

                $extraRateMapId = $extraPayDay->{$timeSlotKey};
                if($extraRateMapId != 0) {
                    $extraRate = ExtraPayRateMap::query()->where('id', $extraRateMapId)->first();
                    $sort_code = $extraRate['pay_rate_short_code'];
                } else {
                    $extraRate = $job_row['pay_rate_details'];
                    $sort_code = 'DR';
                }

                $organizedDataIndex = $job_row['id'] . '_' . $entry['worker_id'] . '_basic_pay_'.$sort_code;
                if (!isset($organizedData[$organizedDataIndex])) {
                    $organizedData[$organizedDataIndex] = $this->generatePayRollReportArray(
                        $site['id'],
                        $job_row['id'],
                        $entry['worker_id'],
                        $entry['worker_details']['payroll_reference'],
                        $payroll_week,
                        'Basic pay',
                        $sort_code,
                        0,
                        0
                    );
                }

                $dailyWorkingHours += 0.5;
                if ($overtime_type === 'hours_per_day' && $overtime_hours_threshold > 0) {
                    if ($dailyWorkingHours > $overtime_hours_threshold) {
                        $perDayOvertimeHours = $dailyWorkingHours - $overtime_hours_threshold;
                    }
                }

                $organizedData[$organizedDataIndex][$dayColumn] += 0.5;
                $organizedData[$organizedDataIndex]['total_hours'] += 0.5;
                $organizedData[$organizedDataIndex]['total_charge'] += $extraRate['base_charge_rate'] / 2;
                $organizedData[$organizedDataIndex]['total_pay'] += $extraRate['base_pay_rate'] / 2;
                $basicPayTotalHoursArrayForPayRateMap[$basicPayCountIndex]['total_hours'] += 0.5;

                if ($perDayOvertimeHours > 0) {
                    $overtimeIndex = $job_row['id'] . '_' . $entry['worker_id'] . '_overtime_'.$sort_code;

                    if (!isset($organizedData[$overtimeIndex])) {
                        $organizedData[$overtimeIndex] = $this->generatePayRollReportArray(
                            $site['id'],
                            $job_row['id'],
                            $entry['worker_id'],
                            $entry['worker_details']['payroll_reference'],
                            $payroll_week,
                            'Overtime',
                            $sort_code,
                            0,
                            0
                        );
                    }

                    $organizedData[$organizedDataIndex][$dayColumn] -= 0.5;
                    $organizedData[$organizedDataIndex]['total_hours'] -= 0.5;
                    $organizedData[$organizedDataIndex]['total_charge'] -= $extraRate['base_charge_rate'] / 2;
                    $organizedData[$organizedDataIndex]['total_pay'] -= $extraRate['base_pay_rate'] / 2;

                    $organizedData[$overtimeIndex][$dayColumn] += 0.5;
                    $organizedData[$overtimeIndex]['total_hours'] += 0.5;
                    $organizedData[$overtimeIndex]['total_charge'] += $extraRate['default_overtime_charge_rate'] / 2;
                    $organizedData[$overtimeIndex]['total_pay'] += $extraRate['default_overtime_pay_rate'] / 2;
                }

                if ($overtime_type === 'hours_per_week' && $overtime_hours_threshold > 0) {
                    $overtime_hours = $basicPayTotalHoursArrayForPayRateMap[$basicPayCountIndex]['total_hours'] - $overtime_hours_threshold;
                    if ($overtime_hours > 0) {
                        $overtimeIndex = $job_row['id'] . '_' . $entry['worker_id'] . '_overtime_'.$sort_code;

                        if (!isset($organizedData[$overtimeIndex])) {
                            $organizedData[$overtimeIndex] = $this->generatePayRollReportArray(
                                $site['id'],
                                $job_row['id'],
                                $entry['worker_id'],
                                $entry['worker_details']['payroll_reference'],
                                $payroll_week,
                                'Overtime',
                                $sort_code,
                                0,
                                0
                            );
                        }

                        $organizedData[$organizedDataIndex][$dayColumn] -= $overtime_hours;
                        $organizedData[$organizedDataIndex]['total_hours'] -= $overtime_hours;
                        $organizedData[$organizedDataIndex]['total_charge'] -= $overtime_hours * $extraRate['base_charge_rate'];
                        $organizedData[$organizedDataIndex]['total_pay'] -= $overtime_hours * $extraRate['base_pay_rate'];
                        $basicPayTotalHoursArrayForPayRateMap[$basicPayCountIndex]['total_hours'] -= 0.5;

                        $organizedData[$overtimeIndex][$dayColumn] += $overtime_hours;
                        $organizedData[$overtimeIndex]['total_hours'] += $overtime_hours;
                        $organizedData[$overtimeIndex]['total_charge'] += $overtime_hours * $extraRate['default_overtime_charge_rate'];
                        $organizedData[$overtimeIndex]['total_pay'] += $overtime_hours * $extraRate['default_overtime_pay_rate'];
                    }
                }

                $current = $next;
            }
        }
        return [
            'organizedData' => $organizedData,
            'basicPayTotalHoursArrayForPayRateMap' => $basicPayTotalHoursArrayForPayRateMap
        ];
    }

    private function generatePayRollReportArray($site_id, $job_id, $worker_id, $worker_payroll_ref, $payroll_week, $pay_rate_name, $rate_name, $charge_rate, $pay_rate) {
        return [
            'site_id'       => $site_id,
            'job_id'        => $job_id,
            'worker_id'     => $worker_id,
            'worker_payroll_ref'=> $worker_payroll_ref,
            'payroll_week'  => $payroll_week,
            'pay_rate_name' => $pay_rate_name,
            'rate_name'     => $rate_name,
            'charge_rate'   => $charge_rate,
            'pay_rate'      => $pay_rate,
            'bonus_type'    => null,
            'day_1_hours'   => 0,
            'day_2_hours'   => 0,
            'day_3_hours'   => 0,
            'day_4_hours'   => 0,
            'day_5_hours'   => 0,
            'day_6_hours'   => 0,
            'day_7_hours'   => 0,
            'total_hours'   => 0,
            'total_charge'  => 0,
            'total_pay'     => 0,
        ];
    }

    private function calculateBonus($job_row, $pwdNode, $site, $payroll_week, $organizedData) {
        $bonusData = Bonus::query()
            ->where('job_id', $job_row['id'])
            ->where('week_number', $pwdNode[0])
            ->where('week_year_number', $pwdNode[1])
            ->with(['worker_details'])
            ->get()
            ->toArray();
        if ($bonusData) {
            foreach ($bonusData as $b_row) {
                $organizedBonusDataIndex = $job_row['id'] . '_' . $b_row['worker_id'].'_'.$b_row['bonus_type'];
                $organizedData[$organizedBonusDataIndex] = [
                    'site_id'       => $site['id'],
                    'job_id'        => $job_row['id'],
                    'worker_id'     => $b_row['worker_id'],
                    'worker_payroll_ref'=> $b_row['worker_details']['payroll_reference'],
                    'payroll_week'  => $payroll_week,
                    'pay_rate_name' => 'Bonus',
                    'rate_name'     => 'Bonus',
                    'bonus_type'    => $b_row['bonus_type'],
                    'charge_rate'   => 0,
                    'pay_rate'      => 0,
                    'day_1_hours'   => 0,
                    'day_2_hours'   => 0,
                    'day_3_hours'   => 0,
                    'day_4_hours'   => 0,
                    'day_5_hours'   => 0,
                    'day_6_hours'   => 0,
                    'day_7_hours'   => 0,
                    'total_hours'   => 0,
                    'total_charge'  => $b_row['bonus_charge_amount'],
                    'total_pay'     => $b_row['bonus_pay_amount'],
                ];
            }
        }
        return $organizedData;
    }

    public function viewPayrollReport(Request $request) {
        $selectedData = [];

        if ($request->get('payroll')) {
            $array = explode('.', $request->get('payroll'));
            $selectedData = [
                'client_id' => $array[0],
                'site_id' => $array[1],
                'payroll_week' => $array[2],
                'sites' => Site::query()->where('client_id', $array[0])->get(),
            ];
        }

        $client = Client::query()->orderBy('company_name')->get();
        $payroll_week_number = PayrollWeekDate::query()->get();
        return view('payroll_report.view_payroll_report', compact('selectedData', 'client', 'payroll_week_number'));
    }

    public function getPayrollData(Request $request) {
        try {
            $params = $request->input();

            $client_name = '';
            $client_logo_url = '';
            $site_name = '';
            $payroll_week = '';
            $pay_date = '';
            $client_week_start = '';
            $date_between = '';
            $payrollLineItem = [];
            $array = [];

            $client = Client::query()->select('id', 'company_name', 'company_logo', 'payroll_week_starts')
                ->where('id', $params['client'])
                ->first();
            if ($client) {

                $client_name = $client['company_name'];
                $client_logo_url = asset('workers/client_document/'.$client['company_logo']);
                $client_week_start = $client['payroll_week_starts'];

                $site = Site::query()->select(['id', 'site_name'])
                    ->where('id', $params['site'])
                    ->first()
                    ->toArray();
                if($site) {

                    $site_name = $site['site_name'];
                    $payroll_week = str_replace('_', '-', $params['pwn']);

                    $payroll_start_date_column = $client['payroll_week_starts'].'_payroll_start';
                    $payroll_end_date_column = $client['payroll_week_starts'].'_payroll_end';

                    $pwdNode = explode('_', $params['pwn']);
                    $pwData = PayrollWeekDate::query()->select(['id', 'payroll_week_number', 'year', $payroll_start_date_column, $payroll_end_date_column, 'pay_date'])
                        ->where('payroll_week_number', $pwdNode[0])
                        ->where('year', $pwdNode[1])
                        ->first();
                    if($pwData) {

                        $pay_date = date('d/m/Y', strtotime($pwData['pay_date']));
                        $date_between = date('d/m/Y', strtotime($pwData[$payroll_start_date_column])).' - '. date('d/m/Y', strtotime($pwData[$payroll_end_date_column]));

                        $payrollLineItem = PayrollLineItem::query()->where('site_id', $params['site'])
                            ->where('payroll_week', $payroll_week)
                            ->with(['worker_details', 'job_details'])
                            ->get()
                            ->toArray();
                        if ($payrollLineItem) {
                            foreach ($payrollLineItem as $row) {
                                $worker_name = $row['worker_details']['first_name'].' '.$row['worker_details']['middle_name'].' '.$row['worker_details']['last_name'];

                                $array[]     = [
                                    'worker_name'   => ($row['worker_details']) ? '<span>'.$worker_name.'<br><small class="text-muted">'.$row['worker_details']['worker_no'].'</small></small></span>' : '-',
                                    'type'          => $row['pay_rate_name'],
                                    'rate'          => ($row['rate_name'] == 'flat_rate') ? '<span class="fw-bolder">Flat rate</span>' : $row['rate_name'],
                                    'job'           => $row['job_details']['name'],
                                    'day_1'         => number_format($row['day_1_hours'], 2),
                                    'day_2'         => number_format($row['day_2_hours'], 2),
                                    'day_3'         => number_format($row['day_3_hours'], 2),
                                    'day_4'         => number_format($row['day_4_hours'], 2),
                                    'day_5'         => number_format($row['day_5_hours'], 2),
                                    'day_6'         => number_format($row['day_6_hours'], 2),
                                    'day_7'         => number_format($row['day_7_hours'], 2),
                                    'total_hours'   => number_format($row['total_hours'], 2),
                                    'total_charge'  => number_format($row['total_charge'], 2),
                                    'total_pay'     => number_format($row['total_pay'], 2),
                                ];
                            }
                        }
                    }
                }
            }

            $request['type'] = 'unlock';
            $timesheetController = new TimeSheetUploaderController();
            $bonusController = new BonusUploaderController();

            return [
                'draw'              => 1,
                'recordsTotal'      => count($payrollLineItem),
                'recordsFiltered'   => count($payrollLineItem),
                'data'              => $array,

                'client_name'       => $client_name,
                'client_logo_url'   => $client_logo_url,
                'site_name'         => $site_name,
                'payroll_week_number'=> $payroll_week,
                'pay_date'          => $pay_date,
                'client_week_start' => $client_week_start,
                'date_between'      => $date_between,
                'total_charge'      => number_format(array_sum(array_column($array, 'total_charge')), 2),
                'total_pay'         => number_format(array_sum(array_column($array, 'total_pay')), 2),
                'reported_edit_or_not' => (Carbon::createFromFormat('d/m/Y', $pay_date)->isFuture())
                    ? '<a href="javascript:;" class="text-danger delete_report fs-4" id="delete_report">Or delete report and return to editor</a>'
                    : '<span class="text-gray-500 fs-5">The pay date for this payroll week has passed. <br> Editing this report is no longer possible.</span>',
                'timesheet_and_bonus_new_entry_created' => ($timesheetController->getTimesheetEditorShiftLineItemData($request)['data'] || $bonusController->getBonusEditorLineItemData($request)['data'])
                    ? url('timesheet-and-bonus-editor?filtered='.$params['client'].'.'.$params['site'].'.'.$params['pwn'])
                    : '',
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function exportPayrollItem(Request $request) {
        $params = $request->input();
        $site = Site::query()->select('site_name', 'client_id')->where('id', $params['site'])->with('client_details')->first();

        $payroll_start_date_column = $site['client_details']['payroll_week_starts'].'_payroll_start';
        $payroll_end_date_column = $site['client_details']['payroll_week_starts'].'_payroll_end';
        $pwdNode = explode('_', $params['payroll_week']);

        $pwData = PayrollWeekDate::query()->select(['id', 'payroll_week_number', $payroll_start_date_column, $payroll_end_date_column, 'pay_date'])
            ->where('payroll_week_number', $pwdNode[0])
            ->where('year', $pwdNode[1])
            ->first();

        $params = array_merge($params, [
            'payroll_week_start' => date('d/m/Y', strtotime($pwData[$payroll_start_date_column])),
            'payroll_week_end' => date('d/m/Y', strtotime($pwData[$payroll_end_date_column])),
        ]);

        $file_name = strtolower('payroll_report_'.str_replace([' ', '/', '\\'], ['_', '-', '-'], $site['site_name']).'_'.$params['payroll_week'].'.csv');
        return (new PayrollLineItemExport($params))->download($file_name);
    }

    public function deleteReport(Request $request) {
        try {
            return $this->deleteReportAction($request->input());
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function deleteReportAction($params) {
        $payroll_week = str_replace('_', '-', $params['pwd']);
        $siteLock = SiteWeekLock::query()
            ->where('site_id', $params['site_id'])
            ->where('payroll_week', $payroll_week)
            ->first();
        if ($siteLock) {
            $siteLock->delete();

            $payrollLineEntries = PayrollLineItem::query()
                ->where('site_id', $params['site_id'])
                ->where('payroll_week', $payroll_week);

            if ($payrollLineEntries->get()) {
                $payrollLineEntries->delete();
            }

            return self::responseWithSuccess('Payroll report successfully deleted');
        }
        return self::responseWithError('This site is not locked.');
    }

    /*public function OldCodeForPayRateCreate() {
        $overtime_hours_threshold = $job_row['pay_rate_details']['default_overtime_hours_threshold'] ?? 0;
        foreach ($dayData as $entry) {
            $organizedDataIndex = $job_row['id'] . '_' . $entry['worker_id'].'_basic_pay';

            if (!isset($organizedData[$organizedDataIndex])) {
                $organizedData[$organizedDataIndex] = [
                    'site_id'       => $site['id'],
                    'job_id'        => $job_row['id'],
                    'worker_id'     => $entry['worker_id'],
                    'worker_payroll_ref'=> $entry['worker_details']['payroll_reference'],
                    'payroll_week'  => $payroll_week,
                    'pay_rate_name' => 'Basic pay',
                    'charge_rate'   => $job_row['pay_rate_details']['base_charge_rate'] ?? 0,
                    'pay_rate'      => $job_row['pay_rate_details']['base_pay_rate'] ?? 0,
                    'bonus_type'    => null,
                    'day_1_hours'   => 0,
                    'day_2_hours'   => 0,
                    'day_3_hours'   => 0,
                    'day_4_hours'   => 0,
                    'day_5_hours'   => 0,
                    'day_6_hours'   => 0,
                    'day_7_hours'   => 0,
                    'total_hours'   => 0,
                    'total_charge'  => 0,
                    'total_pay'     => 0,
                ];
            }

            $dayIndex = $index + 1;
            $dayColumn = 'day_'.$dayIndex.'_hours';

            $hoursWorked = (float) $entry['hours_worked'];
            $organizedData[$organizedDataIndex][$dayColumn] += $hoursWorked;
            $organizedData[$organizedDataIndex]['total_hours'] += $hoursWorked;
            $organizedData[$organizedDataIndex]['total_charge'] = $organizedData[$organizedDataIndex]['total_hours'] * $job_row['pay_rate_details']['base_charge_rate'];
            $organizedData[$organizedDataIndex]['total_pay'] = $organizedData[$organizedDataIndex]['total_hours'] * $job_row['pay_rate_details']['base_pay_rate'];

            $overtime_hours = ($overtime_hours_threshold !== null && $overtime_hours_threshold > 0)
                ? $organizedData[$organizedDataIndex]['total_hours'] - $overtime_hours_threshold
                : 0;

            if ($overtime_hours > 0) {
                $organizedData[$organizedDataIndex][$dayColumn] -= $overtime_hours;
                $organizedData[$organizedDataIndex]['total_hours'] -= $overtime_hours;
                $organizedData[$organizedDataIndex]['total_charge'] -= $overtime_hours * $job_row['pay_rate_details']['base_charge_rate'];
                $organizedData[$organizedDataIndex]['total_pay'] -= $overtime_hours * $job_row['pay_rate_details']['base_pay_rate'];

                $organizedDataIndex = $organizedDataIndex . '_overtime';
                if (!isset($organizedData[$organizedDataIndex])) {
                    $organizedData[$organizedDataIndex] = [
                        'site_id'       => $site['id'],
                        'job_id'        => $job_row['id'],
                        'worker_id'     => $entry['worker_id'],
                        'worker_payroll_ref'=> $entry['worker_details']['payroll_reference'],
                        'payroll_week'  => $payroll_week,
                        'pay_rate_name' => 'Overtime',
                        'charge_rate'   => $job_row['pay_rate_details']['default_overtime_charge_rate'] ?? 0,
                        'pay_rate'      => $job_row['pay_rate_details']['default_overtime_pay_rate'] ?? 0,
                        'bonus_type'    => null,
                        'day_1_hours'   => 0,
                        'day_2_hours'   => 0,
                        'day_3_hours'   => 0,
                        'day_4_hours'   => 0,
                        'day_5_hours'   => 0,
                        'day_6_hours'   => 0,
                        'day_7_hours'   => 0,
                        'total_hours'   => 0,
                        'total_charge'  => 0,
                        'total_pay'     => 0,
                    ];
                }

                $organizedData[$organizedDataIndex][$dayColumn] += $overtime_hours;
                $organizedData[$organizedDataIndex]['total_hours'] += $overtime_hours;
                $organizedData[$organizedDataIndex]['total_charge'] += $overtime_hours * $job_row['pay_rate_details']['default_overtime_charge_rate'];
                $organizedData[$organizedDataIndex]['total_pay'] += $overtime_hours * $job_row['pay_rate_details']['default_overtime_pay_rate'];
            }
        }
    }*/
}
