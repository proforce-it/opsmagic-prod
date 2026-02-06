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

class BookingOverviewController extends Controller
{
    use JsonResponse;
    public function index() {
        $todayDate = Carbon::today()->format('Y-m-d');
        $payroll_week = PayrollWeekDate::query()->select('id', 'payroll_week_number', 'year', 'pay_date', 'monday_payroll_start', 'monday_payroll_end')
            ->where('monday_payroll_start', '<=', $todayDate)
            ->where('monday_payroll_end', '>=', $todayDate)
            ->first();
        $costCentre = CostCentre::query()->get();

        $startDate = Carbon::parse($payroll_week->monday_payroll_start);
        $weekDays = collect(range(1, 7))->mapWithKeys(fn ($no) => [
            "day_{$no}_title" => $startDate->copy()->addDays($no - 1)->format('d M')
        ])->toArray();

        return view('job.booking_overview', compact( 'payroll_week', 'costCentre', 'weekDays'));
    }

    public function changeWeekBookingOverview(Request $request) {
        try {
            $params = $request->input();
            $weekNumber = ($params['type'] == 'backward')
                ? $params['selected_week_number'] - 1
                : $params['selected_week_number'] + 1;

            $payroll_week = PayrollWeekDate::query()->select('id', 'payroll_week_number', 'year', 'pay_date', 'monday_payroll_start')
                ->where('payroll_week_number',  $weekNumber)
                ->where('year',  $params['selected_week_year'])
                ->first();

            $startDate = Carbon::parse($payroll_week->monday_payroll_start);
            return self::responseWithSuccess('Payroll week data.', [
                'payroll_week_id' => $payroll_week->id,
                'selected_week_date' => Carbon::make($payroll_week->monday_payroll_start)->format('d M Y'),
                'selected_week_number' => $payroll_week->payroll_week_number,
                'selected_week_year' => $payroll_week->year,
                'week_days' => collect(range(1, 7))->mapWithKeys(fn ($no) => [
                    "day_{$no}_title" => $startDate->copy()->addDays($no - 1)->format('d M')
                ])->toArray()
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getBookingOverview(Request $request) {
        try {
            $params = $request->input();
            $costCenter = $params['cost_center'];

            $payrollWeek = PayrollWeekDate::query()
                ->where('id', $params['payroll_week_id'])
                ->first();

            if (!$payrollWeek) {
                return self::responseWithError('Payroll week data not found please try again later.');
            }

            /** ---------------- INITIAL STRUCTURES ---------------- */
            $orderFulfilment = $this->getWeeklyShiftArray();
            $overBookings    = $this->getWeeklyShiftArray();
            $clientsData     = [];

            /** ---------------- FETCH CLIENTS ---------------- */
            $clients = Client::query()
                ->with([
                    'client_site_details' => function ($siteQuery) {
                        if (request('cost_center') !== 'Any') {
                            $siteQuery->where('cost_center', request('cost_center'));
                        }
                    },
                    'client_job_details' => function ($jobQuery) use ($payrollWeek, $costCenter) {

                        if ($costCenter !== 'Any') {
                            $jobQuery->whereHas('site_details', function ($siteQuery) use ($costCenter) {
                                $siteQuery->where('cost_center', $costCenter);
                            });
                        }

                        $jobQuery->with([
                            'job_shift_details' => function ($shiftQuery) use ($payrollWeek) {
                                $shiftQuery->whereBetween('date', [
                                    $payrollWeek['monday_payroll_start'],
                                    $payrollWeek['monday_payroll_end'],
                                ]);
                            }
                        ]);
                    }
                ])
                ->orderBy('company_name')
                ->get();

            /** ---------------- AGGREGATE DATA ---------------- */
            foreach ($clients as $client) {

                $clientRow = array_merge(
                    [
                        'client_id' => $client->id,
                        'title' => $client->company_name,
                        'site_count' => $client->client_site_details->count(),
                        'job_count'  => $client->client_job_details->count(),
                    ],
                    $this->getWeeklyShiftArray()
                );

                foreach ($client->client_job_details as $job) {
                    foreach ($job->job_shift_details as $shift) {

                        $day = strtolower(Carbon::parse($shift->date)->format('l'));

                        // CHANGE THESE FIELD NAMES IF NEEDED
                        $requested = (int) $shift->number_workers;
                        $joined = collect($shift->JobShiftWorker_details)
                            ->whereNotNull('confirmed_at')
                            ->whereNull('cancelled_at')
                            ->whereNull('declined_at')
                            ->count();
                        $extras = max($joined - $requested, 0);

                        /** ---------- GLOBAL TOTALS ---------- */
                        $orderFulfilment[$day]['total_no_of_worker'] += $requested;
                        $orderFulfilment[$day]['total_no_of_joined_worker'] += $joined;

                        $clientRow['fulfilment']['total_no_of_worker'] += $requested;
                        $clientRow['fulfilment']['total_no_of_joined_worker'] += $joined;
                        $clientRow['fulfilment']['total_no_of_joined_worker'] -= $extras;
                        $clientRow['extras']['total_no_of_worker'] += $extras;

                        /** ---------- CLIENT TOTALS ---------- */
                        $clientRow[$day]['total_no_of_worker'] += $requested;
                        $clientRow[$day]['total_no_of_joined_worker'] += $joined;
                    }
                }

                $clientsData[] = $clientRow;
            }

            /** ---------------- CALCULATE FULFILMENT & EXTRAS ---------------- */
            $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

            foreach ($days as $day) {

                $requested = $orderFulfilment[$day]['total_no_of_worker'];
                $joined    = $orderFulfilment[$day]['total_no_of_joined_worker'];
                $extras    = max($joined - $requested, 0);

                // Extras row
                $overBookings[$day]['total_no_of_worker'] = $extras;

                // Totals
                $orderFulfilment['fulfilment']['total_no_of_worker'] += $requested;
                $orderFulfilment['fulfilment']['total_no_of_joined_worker'] += $joined;
                $orderFulfilment['fulfilment']['total_no_of_joined_worker'] -= $extras;
                $orderFulfilment['extras']['total_no_of_worker'] += $extras;
                $overBookings['extras']['total_no_of_worker'] += $extras;
            }

            /** ---------------- FINAL RESPONSE ---------------- */
            $response = [
                [
                    'title' => 'Order fulfilment',
                    'data'  => $orderFulfilment,
                ],
                [
                    'title' => 'Overbooking (extras)',
                    'data'  => $overBookings,
                ],
                [
                    'title'   => 'By client',
                    'clients' => $clientsData,
                ],
            ];

            return self::responseWithSuccess('Bookings overview', $response);

        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public static function getWeeklyShiftArray() {
        return [
            'monday' => [
                'total_no_of_joined_worker' => 0,
                'total_no_of_worker' => 0,
            ],
            'tuesday' => [
                'total_no_of_joined_worker' => 0,
                'total_no_of_worker' => 0,
            ],
            'wednesday' => [
                'total_no_of_joined_worker' => 0,
                'total_no_of_worker' => 0,
            ],
            'thursday' => [
                'total_no_of_joined_worker' => 0,
                'total_no_of_worker' => 0,
            ],
            'friday' => [
                'total_no_of_joined_worker' => 0,
                'total_no_of_worker' => 0,
            ],
            'saturday' => [
                'total_no_of_joined_worker' => 0,
                'total_no_of_worker' => 0,
            ],
            'sunday' => [
                'total_no_of_joined_worker' => 0,
                'total_no_of_worker' => 0,
            ],
            'fulfilment' => [
                'total_no_of_joined_worker' => 0,
                'total_no_of_worker' => 0,
            ],
            'extras' => [
                'total_no_of_worker' => 0,
            ],
        ];
    }

    //------------- BOOKING OVERVIEW BY CLIENT -------------//
    public function bookingOverviewByClient(Request $request, $id) {
        $client = Client::query()->select('id', 'company_name', 'company_logo')->where('id', $id)->first();
        $weekExplode = explode('_', $request->input('week'));
        $payroll_week = PayrollWeekDate::query()->select('id', 'payroll_week_number', 'year', 'pay_date', 'monday_payroll_start', 'monday_payroll_end')
            ->where('payroll_week_number',  $weekExplode[0])
            ->where('year', $weekExplode[1])
            ->first();
        $costCentre = CostCentre::query()->get();

        $startDate = Carbon::parse($payroll_week->monday_payroll_start);
        $weekDays = collect(range(1, 7))->mapWithKeys(fn ($no) => [
            "day_{$no}_title" => $startDate->copy()->addDays($no - 1)->format('d M')
        ])->toArray();

        return view('job.booking_overview_by_client', compact( 'client', 'payroll_week', 'costCentre', 'weekDays'));
    }

    public function getBookingOverviewByClient(Request $request) {
        try {
            $params = $request->input();

            $payrollWeek = PayrollWeekDate::query()
                ->where('id', $params['payroll_week_id'])
                ->first();

            if (!$payrollWeek) {
                return self::responseWithError('Payroll week data not found please try again later.');
            }

            /** ---------------- INITIAL STRUCTURES ---------------- */
            $jobsData = [];

            /** ---------------- FETCH CLIENTS ---------------- */
            $client = Client::query()->where('id', $params['client_id'])
                ->with([
                    'client_site_details_with_job' => function ($siteQuery) {
                        if (request('cost_center') !== 'Any') {
                            $siteQuery->where('cost_center', request('cost_center'));
                        }
                    },
                    'client_site_details_with_job.job_details.job_shift_details' => function ($shiftQuery) use ($payrollWeek) {
                        $shiftQuery->whereBetween('date', [
                            $payrollWeek->monday_payroll_start,
                            $payrollWeek->monday_payroll_end,
                        ]);
                    }
                ])
                ->first();

            /** ---------------- AGGREGATE DATA ---------------- */
            foreach ($client->client_site_details_with_job as $site) {
                $siteData = [
                    'site_name' => $site->site_name,
                    'job_data'  => []
                ];
                foreach ($site->job_details as $job) {
                    $jobData = array_merge(
                        [
                            'job_id' => $job->id,
                            'title'  => $job->name,
                        ],
                        $this->getWeeklyShiftArray()
                    );
                    foreach ($job->job_shift_details as $shift) {

                        $day = strtolower(Carbon::parse($shift->date)->format('l'));

                        // CHANGE THESE FIELD NAMES IF NEEDED
                        $requested = (int)$shift->number_workers;
                        $joined = collect($shift->JobShiftWorker_details)
                            ->whereNotNull('confirmed_at')
                            ->whereNull('cancelled_at')
                            ->whereNull('declined_at')
                            ->count();
                        $extras = max($joined - $requested, 0);

                        $jobData['fulfilment']['total_no_of_worker'] += $requested;
                        $jobData['fulfilment']['total_no_of_joined_worker'] += $joined;
                        $jobData['fulfilment']['total_no_of_joined_worker'] -= $extras;
                        $jobData['extras']['total_no_of_worker'] += $extras;

                        /** ---------- CLIENT TOTALS ---------- */
                        $jobData[$day]['total_no_of_worker'] += $requested;
                        $jobData[$day]['total_no_of_joined_worker'] += $joined;
                    }
                    $siteData['job_data'][] = $jobData;
                }
                $jobsData[] = $siteData;
            }

            return self::responseWithSuccess('Bookings overview by client', $jobsData);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
