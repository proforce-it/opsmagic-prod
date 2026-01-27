<?php

namespace App\Http\Controllers\Api\V1\Workers\Timesheet;

use App\Http\Controllers\Controller;
use App\Models\Client\ClientJobWorker;
use App\Models\PickUpPoint\PickUpPoint;
use App\Models\Timesheet\Timesheet;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class TimesheetController extends Controller
{
    use JsonResponse;
    public function index(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'month' => 'required',
                'year' => 'required',
            ]);

            if ($validator->fails()) {
                return self::apiValidationError($validator->errors()->messages());
            }

            $workerId = auth('api')->id();
            $params = $request->input();

            $date = Carbon::create($params['year'], $params['month'], 1);
            $startDate = $date->startOfMonth()->toDateString();
            $endDate   = $date->endOfMonth()->toDateString();

            $timesheet = Timesheet::query()
                ->where('worker_id', $workerId)
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->with('jobShift')
                ->get();

            $items = [];
            $totalHours = 0;

            foreach ($timesheet as $row) {
                $jobShift = $row->jobShift;
                $startTime = $row->in_time ?? $jobShift->start_time;

                $hrsWorker = explode('.', $row->hours_worked);
                $hours = (int)$hrsWorker[0];
                $fraction = isset($hrsWorker[1]) ? ('0.' . $hrsWorker[1]) : 0;
                $minutes = $fraction * 60;
                $decimal = $hours + ($minutes / 60);
                $totalHours += $decimal;

                $clientJobWorker = ClientJobWorker::query()->where('job_id', $row->jobShift->job_id)
                    ->where('worker_id', $row->worker_id)
                    ->with(['worker.preferred_pickup_point'])
                    ->first();

                $pickupDetails = '';
                if ($clientJobWorker) {
                    $getPickupDetails = function ($pickupPointId) {
                        $pickupPoint = PickUpPoint::query()->where('id', $pickupPointId)->first();
                        if ($pickupPoint) {
                            return $pickupPoint->name . ' - ///' . $pickupPoint->what_three_words_locator;
                        }
                        return null;
                    };

                    if ($clientJobWorker['agreed_pickup_point'] === '0') {
                        $pickupDetails = 'None (no pickup required)';
                    } elseif ($clientJobWorker['agreed_pickup_point'] === '00') {
                        $pickupDetails = 'Same as preferred';
                    } elseif ($clientJobWorker['agreed_pickup_point']) {
                        $pickupDetails = $getPickupDetails($clientJobWorker['agreed_pickup_point']);
                    } else {
                        $pickupDetails = ($clientJobWorker['worker']['preferred_pickup_point'])
                            ? $getPickupDetails($clientJobWorker['worker']['preferred_pickup_point']['id'])
                            : '';
                    }
                }

                $items[] = [
                    'id'          => $row->id,
                    'date'        => Carbon::parse($row->date)->format('D d M Y'),
                    'time'        => Carbon::parse($startTime)->format('Hi'),
                    'duration'    => "{$hours}h{$minutes}m",
                    'client_name' => $jobShift->client_job_details->client_details->company_name,
                    'site_name'   => $jobShift->client_job_details->site_details->site_name,
                    'job_name'    => $jobShift->client_job_details->name,
                    'pickup_point' => $pickupDetails
                ];
            }

            return self::responseWithSuccess('Timesheet details fetched successfully.', [
                'month' => strtoupper($date->format('M')).' '.$date->format('Y'),
                'total_hours' => number_format($totalHours, 2).' hours',
                'count' => count($items).' shifts',
                'items' => $items,
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
