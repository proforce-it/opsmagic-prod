<?php

namespace App\Http\Controllers\Api\V1\Workers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Worker\Worker;
use App\Models\Worker\WorkerPendingRequest;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AbsenceController extends Controller
{
    use JsonResponse;
    public function index(Request $request) {
        try {
            $startDateAfter = now()->addDays(14)->format('d/m/Y');
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date|after_or_equal:' . now()->addDays(14)->toDateString(),
                'end_date' => 'nullable|date|after:start_date|different:start_date',
                //'reason' => 'required|in:Holiday,Other,Sickness,Rest',
            ],[
                'start_date.after_or_equal' => 'The start date must be a date after or equal to ' . $startDateAfter . '.',
            ]);

            if ($validator->fails()) {
                return self::apiValidationError($validator->errors()->messages());
            }

            $workerId = auth('api')->id();
            $params = $request->input();

            $worker = Worker::query()
                ->where('id', $workerId)
                ->first();

            if (!$worker) {
                return self::responseWithError('Worker not found, please try again later.');
            }

            $checkPendingRequest = WorkerPendingRequest::query()
                ->where('worker_id', $workerId)
                ->where('type', 'absence')
                ->exists();
            if ($checkPendingRequest) {
                return self::responseWithError('A request has already been submitted and is currently pending. A new request cannot be created.');
            }

            $createdRecord = WorkerPendingRequest::query()->create([
                'worker_id' => $workerId,
                'type' => 'absence',
                'requested_data' => json_encode([
                    'start_date' => Carbon::parse($params['start_date'])->format('Y-m-d'),
                    'end_date' => ($params['end_date'])
                        ? Carbon::parse($params['end_date'])->format('Y-m-d')
                        : null,
                    'absence_type' => 'Holiday', //$params['reason']
                ])
            ]);

            $requestedData = json_decode($createdRecord['requested_data']);
            return self::responseWithSuccess('Your holiday request has been successfully submitted and is currently pending approval.', [
                'id' => $createdRecord['id'],
                'worker_id' => $createdRecord['worker_id'],
                'start_date' => Carbon::parse($requestedData->start_date)->format('d-m-Y'),
                'end_date' => ($requestedData->end_date) ? Carbon::parse($requestedData->end_date)->format('d-m-Y') : null,
                'reason' => $requestedData->absence_type,
                'created_at' => $createdRecord['created_at'],
                'updated_at' => $createdRecord['updated_at'],
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getMyHolidayRequest(Request $request) {
        try {
            $workerId = auth('api')->id();
            $worker = Worker::query()->where('id', $workerId)->first();

            if (!$worker) {
                return self::responseWithError('Worker not found, please try again later.');
            }

            $myRequest = WorkerPendingRequest::withTrashed()
                ->where('worker_id', $workerId)
                ->where('type', 'absence')
                ->whereDate('requested_data->end_date', '>=', Carbon::now()->toDateString())
                ->orderBy('id', 'desc')
                ->get();

            $returnArray = [];
            if ($myRequest) {
                foreach ($myRequest as $row) {
                    $requestedData = json_decode($row['requested_data']);

                    $status = '';
                    if (is_null($row['declined_by']) && is_null($row['declined_id']) && is_null($row['deleted_at'])) {
                        $status = 'Pending';
                    } elseif (is_null($row['declined_by']) && is_null($row['declined_id']) && !is_null($row['deleted_at'])) {
                        $status = 'Approved';
                    } elseif (!is_null($row['declined_by']) && !is_null($row['declined_id']) && !is_null($row['deleted_at'])) {
                        $status = 'Rejected';
                    }

                    $returnArray[] = [
                        'id' => $row['id'],
                        'worker_id' => $row['worker_id'],
                        'start_date' => Carbon::parse($requestedData->start_date)->format('d-m-Y'),
                        'end_date' => ($requestedData->end_date) ? Carbon::parse($requestedData->end_date)->format('d-m-Y') : null,
                        'reason' => $requestedData->absence_type,
                        'status' => $status,
                        'created_at' => $row['created_at'],
                        'updated_at' => $row['updated_at'],
                    ];
                }
            }
            return self::responseWithSuccess('Your holiday requested data.', $returnArray);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function declinedHolidayRequest(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return self::apiValidationError($validator->errors()->messages());
            }

            $workerId = auth('api')->id();
            $worker = Worker::query()
                ->where('id', $workerId)
                ->first();

            if (!$worker) {
                return self::responseWithError('Worker not found, please try again later.');
            }

            $checkPendingRequest = WorkerPendingRequest::query()->where('id', $request->input('id'))->exists();
            if (!$checkPendingRequest) {
                return self::responseWithError('Your pending holiday request not found.');
            }

            WorkerPendingRequest::query()->where('id', $request->input('id'))->update([
                'declined_by' => 'worker',
                'declined_id' => $workerId
            ]);
            WorkerPendingRequest::query()->where('id', $request->input('id'))->delete();
            return self::responseWithSuccess('Your holiday request has been successfully declined.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
