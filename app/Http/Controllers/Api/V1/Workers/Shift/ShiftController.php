<?php

namespace App\Http\Controllers\Api\V1\Workers\Shift;

use App\Http\Controllers\Controller;
use App\Models\Client\ClientJobWorker;
use App\Models\Job\JobShift;
use App\Models\Job\JobShiftWorker;
use App\Models\PickUpPoint\PickUpPoint;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShiftController extends Controller
{
    use JsonResponse;
    public function index() {
        try {
            $workerId = auth('api')->id();

            $shifts = JobShiftWorker::query()
                ->where('worker_id', $workerId)
                ->whereDate('shift_date', '>=', Carbon::now()->format('Y-m-d'))
                ->with('jobShift.client_job_details.client_details')
                ->with('jobShift.client_job_details.site_details')
                ->get();

            $confirmed = $this->formatShifts(
                $shifts->filter(fn ($s) =>
                    $s->confirmed_at &&
                    !$s->declined_at &&
                    !$s->cancelled_at
                )
            );

            $invitation = $this->formatShifts(
                $shifts->filter(fn ($s) =>
                    $s->invited_at &&
                    !$s->confirmed_at &&
                    !$s->declined_at &&
                    !$s->cancelled_at
                )
            );

            return self::responseWithSuccess('Shift details fetched successfully.', [
                'hours_and_count' => [
                    'confirmed_shift'         => $confirmed['count'],
                    'invitation_shift'        => $invitation['count'],
                    'confirmed_shift_hours'   => $confirmed['total_hours'],
                    'invitation_shift_hours'  => $invitation['total_hours'],
                ],
                'confirmed_shifts'   => $confirmed['items'],
                'invitation_shifts'  => $invitation['items'],
            ]);

        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function formatShifts($collection) {
        $items = [];
        $totalHours = 0;

        foreach ($collection as $row) {
            $jobShift = $row->jobShift;
            $startTime = $row->start_time ?? $jobShift->start_time;

            $hours   = (int) $jobShift->shift_length_hr;
            $minutes = (int) $jobShift->shift_length_min;
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
                'date'        => Carbon::parse($row->shift_date)->format('D d M Y'),
                'time'        => Carbon::parse($startTime)->format('Hi'),
                'duration'    => "{$hours}.{$minutes}",
                'client_name' => $jobShift->client_job_details->client_details->company_name,
                'site_name'   => $jobShift->client_job_details->site_details->site_name,
                'job_name'    => $jobShift->client_job_details->name,
                'pickup_point' => $pickupDetails,
                'client_image' => isset($jobShift->client_job_details->client_details->company_logo) ? asset('workers/client_document/'.$jobShift->client_job_details->client_details->company_logo) : null
            ];
        }

        return [
            'items'       => $items,
            'total_hours' => number_format($totalHours, 2),
            'count'       => count($items),
        ];
    }

    public function invitationShiftAction(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'status' => 'required', // 0 = declined, 1 = confirmed
            ]);

            if ($validator->fails()) {
                return self::apiValidationError($validator->errors()->messages());
            }

            $workerId = auth('api')->id();
            $params = $request->input();

            $jobShiftWorker = JobShiftWorker::query()->where('id', $params['id'])->first();
            if (!$jobShiftWorker) {
                return self::responseWithError('Job shift worker details not found, please try again later.');
            }

            if ($jobShiftWorker['worker_id'] != $workerId) {
                return self::responseWithError('Invalid invitation id passed, please pass valid invitation id.');
            }

            $jobShift = JobShift::query()->where('id', $jobShiftWorker['job_shift_id'])->first();
            if (!$jobShift)
                return self::responseWithError('Job shift details not found, please try again later');

            if ($jobShift['cancelled_at']) {
                return self::responseWithError('This shift is cancelled, Thank you.');
            }

            $JobShiftWorker = JobShiftWorker::query()->where('id', $params['id'])->first();
            if ($JobShiftWorker['confirmed_at'] != null) {
                return self::responseWithError('Invitation already accepted.');
            }

            if ($JobShiftWorker['declined_at'] != null) {
                return self::responseWithError('Invitation already declined.');
            }

            if ($params['status'] == '1') {
                JobShiftWorker::query()->where('id', $params['id'])->update([
                    'confirmed_at' => \Carbon\Carbon::now(),
                ]);
                return self::responseWithSuccess('Thank you for accepted our invitation.');

            } else if ($params['status'] == '0') {
                JobShiftWorker::query()->where('id', $params['id'])->update([
                    'declined_at' => Carbon::now(),
                    'cancelled_by' => 'worker',
                    'cancelled_by_user_id' => $jobShiftWorker['worker_id'],
                ]);
                return self::responseWithSuccess('Invitation successfully declined.');

            } else {
                return self::responseWithError('Invalid status code.');
            }

        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
