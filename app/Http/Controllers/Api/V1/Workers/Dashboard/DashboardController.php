<?php

namespace App\Http\Controllers\Api\V1\Workers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Client\ClientJobWorker;
use App\Models\Job\JobShiftWorker;
use App\Models\PickUpPoint\PickUpPoint;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use JsonResponse;
    public function index(Request $request) {
        try {
            $workerId = auth('api')->id();
            $invitationShiftCount = JobShiftWorker::query()
                ->where('worker_id', $workerId)
                ->whereNotNull('invited_at')
                ->whereNull('confirmed_at')
                ->whereNull('declined_at')
                ->whereNull('cancelled_at')
                ->whereDate('shift_date', '>=', Carbon::now()->format('Y-m-d'))
                ->count();

            $confirmShift = JobShiftWorker::query()
                ->where('worker_id', $workerId)
                ->whereNotNull('confirmed_at')
                ->whereNull('declined_at')
                ->whereNull('cancelled_at')
                ->where(function ($query) {
                    $query->whereDate('shift_date', '>=', Carbon::now()->format('Y-m-d'))
                        ->orWhere(function ($query) {
                            $query->whereDate('shift_date', '>', Carbon::now()->format('Y-m-d'))
                                ->whereTime('start_time', '>=', Carbon::now()->format('H:i:s'));
                        });
                })
                ->with('jobShift.client_job_details.client_details')
                ->with('jobShift.client_job_details.site_details')
                ->get();

            $confirmShiftNode = [];
            if ($confirmShift) {
                foreach ($confirmShift as $cs) {
                    $confirmShiftNode[] = $this->preparedDashboardShiftData($cs);
                }
            }

            $cancelledShift = JobShiftWorker::query()
                ->where('worker_id', $workerId)
                ->whereNotNull('confirmed_at')
                ->whereNull('declined_at')
                ->whereNotNull('cancelled_at')
                ->where(function ($query) {
                    $query->whereDate('shift_date', '>=', Carbon::now()->format('Y-m-d'))
                        ->orWhere(function ($query) {
                            $query->whereDate('shift_date', '>', Carbon::now()->format('Y-m-d'))
                                ->whereTime('start_time', '>=', Carbon::now()->format('H:i:s'));
                        });
                })
                ->with('jobShift.client_job_details.client_details')
                ->with('jobShift.client_job_details.site_details')
                ->get();

            $cancelledShiftNode = [];
            if ($cancelledShift) {
                foreach ($cancelledShift as $cans) {
                    $cancelledShiftNode[] = $this->preparedDashboardShiftData($cans);
                }
            }

            return self::responseWithSuccess('Dashboard details fetched successfully.', [
                'invitation_shift_count' => $invitationShiftCount,
                'confirm_shift' => $confirmShiftNode,
                'cancelled_shift' => $cancelledShiftNode
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function preparedDashboardShiftData($data) {
        if ($data) {
            $jobShift = $data->jobShift;
            $startTime = $data->start_time ?? $jobShift->start_time;

            $hours   = (int) $jobShift->shift_length_hr;
            $minutes = (int) $jobShift->shift_length_min;

            $clientJobWorker = ClientJobWorker::query()->where('job_id', $data->jobShift->job_id)
                ->where('worker_id', $data->worker_id)
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

            return [
                'id'          => $data->id,
                'date'        => Carbon::parse($data->shift_date)->format('D d M Y'),
                'time'        => Carbon::parse($startTime)->format('Hi'),
                'duration'    => "{$hours}.{$minutes}",
                'client_name' => $jobShift->client_job_details->client_details->company_name,
                'site_name'   => $jobShift->client_job_details->site_details->site_name,
                'job_name'    => $jobShift->client_job_details->name,
                'pickup_point' => $pickupDetails,
                'client_image' => isset($jobShift->client_job_details->client_details->company_logo)
                    ? asset('workers/client_document/'.$jobShift->client_job_details->client_details->company_logo)
                    : null
            ];
        } else {
            return [];
        }
    }
}
