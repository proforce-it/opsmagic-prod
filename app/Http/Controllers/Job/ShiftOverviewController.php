<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Models\Client\Client;
use App\Models\Group\CostCentre;
use App\Models\Job\JobShift;
use App\Models\Payroll\PayrollWeekDate;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftOverviewController extends Controller
{
    use JsonResponse;
    public function index() {
        $todayDate = Carbon::today()->format('Y-m-d');
        $client = Client::query()->orderBy('company_name')->where('status', 'Active')->get();
        $payroll_week = PayrollWeekDate::query()->select('id', 'payroll_week_number', 'year', 'pay_date', 'monday_payroll_start')
            ->where('monday_payroll_start', '<=', $todayDate)
            ->where('monday_payroll_end', '>=', $todayDate)
            ->first();
        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('job.shift_overview', compact('client', 'payroll_week', 'costCentre'));
    }

    public function changeWeek(Request $request) {
        try {
            $params = $request->input();
            $weekNumber = ($params['type'] == 'backward')
                ? $params['selected_week_number'] - 1
                : $params['selected_week_number'] + 1;

            $payroll_week = PayrollWeekDate::query()->select('id', 'payroll_week_number', 'year', 'pay_date', 'monday_payroll_start')
                ->where('payroll_week_number',  $weekNumber)
                ->where('year',  $params['selected_week_year'])
                ->first();

            return self::responseWithSuccess('Payroll week data.', [
                'payroll_week_id' => $payroll_week->id,
                'selected_week_date' => Carbon::make($payroll_week->monday_payroll_start)->format('d M Y'),
                'selected_week_number' => $payroll_week->payroll_week_number,
                'selected_week_year' => $payroll_week->year
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getJobShiftOverview(Request $request) {
        try {
            $params = $request->input();
            $payrollWeek = PayrollWeekDate::query()->where('id', $params['payroll_week_id'])->first();
            if (!$payrollWeek) {
                return self::responseWithError('Payroll week data not found please try again later.');
            }

            $jobShifts = JobShift::query()->where('date', '>=', $payrollWeek['monday_payroll_start'])
                ->where('date', '<=', $payrollWeek['monday_payroll_end'])
                ->when(request('cost_center') != 'Any', function ($query) {
                    $query->whereHas('client_job_details.site_details', function ($query3) {
                        $query3->where('cost_center', request('cost_center'));
                    });
                })
                ->when(request('client') != 'Any', function ($query) {
                    $query->whereHas('client_job_details.client_details', function ($query3) {
                        $query3->where('id', request('client'));
                    });
                })
                ->when(request('site') != null, function ($query) {
                    $query->whereHas('client_job_details.site_details', function ($query3) {
                        $query3->where('id', request('site'));
                    });
                })
                ->with(['client_job_details', 'JobShiftWorker_details'])
                ->orderBy('start_time', 'asc')
                ->get();

            $weeklyShifts = [
                'monday'    => '',
                'tuesday'   => '',
                'wednesday' => '',
                'thursday'  => '',
                'friday'    => '',
                'saturday'  => '',
                'sunday'    => '',
            ];
            $shiftCounters = [];

            foreach ($jobShifts as $shift) {
                $dayName = strtolower(Carbon::parse($shift->date)->format('l'));
                if (!isset($shiftCounters[$dayName])) {
                    $shiftCounters[$dayName] = 0;
                }

                $client = optional($shift->client_job_details->client_details)->company_name ?? 'Unknown Client';
                $jobTitle = $shift->client_job_details->name ?? 'Unknown Title';
                $site = optional($shift->client_job_details->site_details)->site_name ?? 'Unknown Site';
                $startTime = Carbon::parse($shift->start_time)->format('Hi') ?? '0000';
                $slots = ($shift->number_workers != 0) ? $shift->number_workers : '-';

                $confirmedCount  = $shift->JobShiftWorker_details
                    ->whereNotNull('confirmed_at')
                    ->whereNull('declined_at')
                    ->whereNull('cancelled_at')
                    ->count();
                $confirmedWorker = ($confirmedCount != 0) ? $confirmedCount : '0';
                $redColor = (($confirmedCount == $shift->number_workers || $slots == '-')) ? '' : 'red';

                /*$isRequiredUnset = $shift->number_workers == null || $shift->number_workers == 0;
                $greenBorder = ($isRequiredUnset && $confirmedCount > 0) || (!$isRequiredUnset && $confirmedCount === $shift->number_workers);
                $redColor = $greenBorder ? '' : 'red';*/

                $url = url('view-job-shift/'.$shift->id);

                $tooltip = htmlspecialchars("{$client} <br> <strong>{$jobTitle}</strong> <br> {$site}");
                $marginTop = $shiftCounters[$dayName] > 0 ? 'mt-4' : '';
                $html = <<<HTML
                    <a href="{$url}" class="text-dark">
                        <div class="lozenge {$redColor} {$marginTop}" data-bs-toggle="tooltip" data-bs-html="true" title="{$tooltip}">
                            <div class="p-1 lozenge-text">
                                <span>{$client}</span><br>
                                <span class="fw-bolder">{$jobTitle}</span><br>
                                <span>{$site}</span>
                            </div>
                            <div class="bg-gray-300 custom-rounded-bottom-9 p-1">
                                <span class="time">
                                    <i class="las la-clock fs-4 text-dark"></i> {$startTime}
                                </span>
                                <span class="slots float-end">
                                    <i class="las la-user-circle fs-4 text-dark"></i> {$confirmedWorker}/{$slots}
                                </span>
                            </div>
                        </div>
                    </a>
                HTML;

                $weeklyShifts[$dayName] .= $html;
                $shiftCounters[$dayName]++;
            }

            return self::responseWithSuccess('Bookings overviews', $weeklyShifts);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
