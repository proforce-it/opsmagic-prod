<?php

namespace App\Http\Controllers\Api\Workers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Accommodation\Accommodation;
use App\Models\Location\Country;
use App\Models\Worker\Worker;
use App\Models\Worker\WorkerPendingRequest;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use JsonResponse;
    public function index(Request $request) {
        try {
            $workerId = auth('api')->id();

            $worker = Worker::query()
                ->select([
                    'worker_no', 'title', 'first_name', 'middle_name', 'last_name',
                    'date_of_birth', 'gender', 'email_address', 'mobile_number',
                    'same_as_current_address', 'current_address_line_one',
                    'current_address_line_two', 'current_country',
                    'current_state', 'current_city', 'current_post_code',
                    'permanent_address_line_one', 'permanent_address_line_two',
                    'permanent_country', 'permanent_state', 'permanent_city',
                    'permanent_post_code', 'accommodation_type', 'accommodation_site'
                ])
                ->where('id', $workerId)
                ->first();

            if (!$worker) {
                return self::responseWithError('Worker not found, please try again later.');
            }

            if ($worker->date_of_birth) {
                $worker->date_of_birth = Carbon::parse($worker->date_of_birth)->format('d/m/Y');
            }

            if ($worker->same_as_current_address == 1) {
                $worker->permanent_address_line_one = $worker->current_address_line_one;
                $worker->permanent_address_line_two = $worker->current_address_line_two;
                $worker->permanent_country = $worker->current_country;
                $worker->permanent_state = $worker->current_state;
                $worker->permanent_city = $worker->current_city;
                $worker->permanent_post_code = $worker->current_post_code;
            }

            if ($worker->accommodation_type == 'supplied_by_pro_force') {
                $accommodationSite = Accommodation::query()->where('id', $worker->accommodation_site)->first();
                $worker->accommodation_site = ($accommodationSite) ? $accommodationSite->name.' - '.$accommodationSite->postcode : null;
            }

            return self::responseWithSuccess('RTWs details fetched successfully.', $worker);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function countryOption() {
        try {
            $workerId = auth('api')->id();
            $worker = Worker::query()
                ->where('id', $workerId)
                ->first();

            if (!$worker) {
                return self::responseWithError('Worker not found, please try again later.');
            }

            return self::responseWithSuccess('Country option', Country::query()->pluck('name'));
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateAddress(Request $request) {
        try {
            $workerId = auth('api')->id();
            $validator = Validator::make($request->all(), [
                'accommodation_type' => 'required|in:arranged_by_worker,supplied_by_pro_force',
            ]);

            if ($validator->fails()) {
                return self::apiValidationError($validator->errors()->messages());
            }

            $params = $request->input();
            if ($params['accommodation_type'] == 'arranged_by_worker') {
                $validator = Validator::make($request->all(), [
                    'address_line_one' => 'required',
                    'city' => 'required',
                    'post_code' => 'required',
                    'country' => 'nullable|exists:countries,name',
                    'mobile_number' => 'required|unique:workers,mobile_number,'.$workerId,
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'mobile_number' => 'required|unique:workers,mobile_number,'.$workerId,
                ]);
            }

            if ($validator->fails()) {
                return self::apiValidationError($validator->errors()->messages());
            }

            $worker = Worker::query()->where('id', $workerId)->first();
            if (!$worker) {
                return self::responseWithError('Worker not found, please try again later.');
            }

            $checkPendingRequest = WorkerPendingRequest::query()->where('worker_id', $workerId)->where('type', 'worker_addresses')->exists();
            if ($checkPendingRequest) {
                return self::responseWithError('A address updating request has already been submitted and is currently pending. A new request cannot be created.');
            }

            $requestedData = [
                'mobile_number' => $params['mobile_number'],
                'accommodation_type' => $params['accommodation_type']
            ];

            if ($params['accommodation_type'] == 'arranged_by_worker') {
                $requestedData = array_merge($requestedData, [
                    'address_line_one' => $params['address_line_one'],
                    'address_line_two' => $params['address_line_two'] ?? null,
                    'city' => $params['city'],
                    'county' => $params['county'] ?? null,
                    'post_code' => $params['post_code'],
                    'country' => $params['country'] ?? null,
                ]);
            }

            $createdRecord = WorkerPendingRequest::query()->create([
                'worker_id' => $workerId,
                'type' => 'worker_addresses',
                'requested_data' => json_encode($requestedData)
            ]);

            $requestedData = json_decode($createdRecord['requested_data']);
            return self::responseWithSuccess('Your address updating request has been successfully submitted and is currently pending approval.', [
                'id' => $createdRecord['id'],
                'worker_id' => $createdRecord['worker_id'],
                'address_line_one' => $requestedData->address_line_one ?? null,
                'address_line_two' => $requestedData->address_line_two ?? null,
                'city' => $requestedData->city ?? null,
                'county' => $requestedData->county ?? null,
                'post_code' => $requestedData->post_code ?? null,
                'country' => $requestedData->country ?? null,
                'mobile_number' => $requestedData->mobile_number,
                'created_at' => $createdRecord['created_at'],
                'updated_at' => $createdRecord['updated_at'],
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getUpdateAddressRequest(Request $request)
    {
        try {
            $workerId = auth('api')->id();
            $worker = Worker::query()->where('id', $workerId)->first();

            if (!$worker) {
                return self::responseWithError('Worker not found, please try again later.');
            }

            $myRequest = WorkerPendingRequest::withTrashed()
                ->where('worker_id', $workerId)
                ->where('type', 'worker_addresses')
                ->orderBy('id', 'desc')
                ->first();

            $returnArray = [];

            if ($myRequest) {
                $requestedData = json_decode($myRequest['requested_data']);

                $status = '';
                if (is_null($myRequest['declined_by']) && is_null($myRequest['declined_id']) && is_null($myRequest['deleted_at'])) {
                    $status = 'Pending';
                } elseif (is_null($myRequest['declined_by']) && is_null($myRequest['declined_id']) && !is_null($myRequest['deleted_at'])) {
                    $status = 'Approved';
                } elseif (!is_null($myRequest['declined_by']) && !is_null($myRequest['declined_id']) && !is_null($myRequest['deleted_at'])) {
                    $status = 'Rejected';
                }

                $returnArray = [
                    'id' => $myRequest['id'],
                    'worker_id' => $myRequest['worker_id'],
                    'accommodation_type' => $requestedData->accommodation_type ?? null,
                    'address_line_one' => $requestedData->address_line_one ?? null,
                    'address_line_two' => $requestedData->address_line_two ?? null,
                    'city' => $requestedData->city ?? null,
                    'county' => $requestedData->county ?? null,
                    'post_code' => $requestedData->post_code ?? null,
                    'country' => $requestedData->country ?? null,
                    'mobile_number' => $requestedData->mobile_number ?? null,
                    'status' => $status,
                    'created_at' => $myRequest['created_at'],
                    'updated_at' => $myRequest['updated_at'],
                ];
            }

            return self::responseWithSuccess('Your address update request data.', $returnArray);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}