<?php

namespace App\Http\Controllers\PendingRequest;

use App\Http\Controllers\Controller;
use App\Models\Worker\Absence;
use App\Models\Worker\Worker;
use App\Models\Worker\WorkerPendingRequest;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PendingRequestController extends Controller
{
    use JsonResponse;
    public function getAbsenceRequest(){
        return view('pending_requests.dis_absence_request');
    }

    public function storeAbsenceRequest(Request $request) {
        try {
            $getPendingRequest = WorkerPendingRequest::query()
                ->where('type', 'absence')
                ->with(['worker'])
                ->orderBy('id', 'desc')
                ->get();

            $array  = [];
            if ($getPendingRequest) {
                foreach ($getPendingRequest as $row) {
                    $requestedData = json_decode($row['requested_data'], true);
                    $array[] = [
                        'request_id' => $row['id'],
                        'worker_name'   => '<a href="'.url('view-worker-details/'.$row['worker']['id']).'" target="_blank">'.$row['worker']['first_name'].' '.$row['worker']['middle_name'].' '.$row['worker']['last_name'].'</a>',
                        'start_date' => ($requestedData['start_date']) ? Carbon::parse($requestedData['start_date'])->format('d-m-Y') : '-',
                        'end_date' => ($requestedData['end_date']) ? Carbon::parse($requestedData['end_date'])->format('d-m-Y') : '-',
                        'reason' => $requestedData['absence_type'],
                        'generated_at' => date('d-m-Y h:i:s', strtotime($row['created_at'])),
                        'action' => $this->action($row['id']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($getPendingRequest),
                'recordsFiltered'   => count($getPendingRequest),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function action($id) {
        return '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm" id="approve_request" data-id="'.$id.'">
                <i class="fs-2 las la-check-circle"></i>
            </a>
            <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="declined_request" data-id="'.$id.'">
                <i class="fs-2 las la-times-circle"></i>
            </a>';
    }

    public function approvedPendingRequest(Request $request) {
        DB::beginTransaction();
        try {
            $params = $request->input();

            $requestData = WorkerPendingRequest::query()->where('id', $params['id'])->first();
            if (!$requestData) {
                throw new \Exception('Invalid request id passed, please passed valid request id.');
            }

            $jsonDecode = json_decode($requestData->requested_data, true);
            if ($params['type'] == 'absence') {
                Absence::query()->create([
                    'worker_id' => $requestData->worker_id,
                    'absence_type' => $jsonDecode['absence_type'],
                    'start_date' => $jsonDecode['start_date'],
                    'end_date' => $jsonDecode['end_date'],
                    'added_by' => Auth::id(),
                ]);
                WorkerPendingRequest::query()->where('id', $params['id'])->delete();

            } else if ($params['type'] == 'worker_addresses') {

                $updatedArray = [
                    'mobile_number' => $jsonDecode['mobile_number'],
                ];
                if ($jsonDecode['accommodation_type'] == 'arranged_by_worker') {
                    $updatedArray = array_merge($updatedArray, [
                        'current_address_line_one' => $jsonDecode['address_line_one'],
                        'current_address_line_two' => $jsonDecode['address_line_two'],
                        'current_city' => $jsonDecode['city'],
                        'current_state' => $jsonDecode['county'],
                        'current_post_code' => $jsonDecode['post_code'],
                        'current_country' => $jsonDecode['country'],
                    ]);
                }
                Worker::query()->where('id', $requestData['worker_id'])->update($updatedArray);
                WorkerPendingRequest::query()->where('id', $params['id'])->delete();
            } else {
                throw new \Exception('Invalid request type passed.');
            }

            DB::commit();
            return self::responseWithSuccess('Request successfully approved.');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responseWithError($e->getMessage());
        }
    }

    public function declinedPendingRequest(Request $request) {
        try {
            $params = $request->input();

            $requestData = WorkerPendingRequest::query()->where('id', $params['id'])->first();
            if (!$requestData) {
                throw new \Exception('Invalid request id passed, please passed valid request id.');
            }

            WorkerPendingRequest::query()->where('id', $params['id'])->update([
                'declined_by' => 'admin',
                'declined_id' => Auth::id()
            ]);
            WorkerPendingRequest::query()->where('id', $params['id'])->delete();
            return self::responseWithSuccess('Request successfully declined.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function getAddressRequest() {
        return view('pending_requests.dis_address_request');
    }

    public function storeAddressRequest(Request $request) {
        try {
            $getAddressRequest = WorkerPendingRequest::query()
                ->where('type', 'worker_addresses')
                ->with(['worker'])
                ->get();

            $array  = [];
            if ($getAddressRequest) {
                foreach ($getAddressRequest as $row) {
                    $currentAddressParts = [
                            $row->worker->current_address_line_one ?? null,
                            $row->worker->current_address_line_two ?? null,
                            $row->worker->current_city ?? null,
                            $row->worker->current_state ?? null,
                            $row->worker->current_country ?? null,
                            $row->worker->current_post_code ?? null,
                    ];
                    $currentAddress = implode(', ', array_filter($currentAddressParts, fn($value) => !empty($value)));

                    $requestedData = json_decode($row['requested_data'], true);
                    $addressParts = [
                            $requestedData['address_line_one'] ?? null,
                            $requestedData['address_line_two'] ?? null,
                            $requestedData['city'] ?? null,
                            $requestedData['county'] ?? null,
                            $requestedData['country'] ?? null,
                            $requestedData['post_code'] ?? null,
                    ];
                    $requestedAddress = implode(', ', array_filter($addressParts, function ($value) { return !empty($value); }));
                    $worker_name = $row['worker']['first_name'].' '.$row['worker']['middle_name'].' '.$row['worker']['last_name'];

                    $array[] = [
                        'worker_name' => '<a href="'.url('view-worker-details/'.$row['worker']['id']).'" target="_blank">'.$worker_name.'</a>',
                        'mobile_number' => $row->worker->mobile_number,
                        'generated_at' => date('d-m-Y h:i:s', strtotime($row['created_at'])),
                        'details' => '<a href="javascript:;" 
                            class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" 
                            id="view_addresses"
                            data-worker_name="'.strtolower($worker_name).'" 
                            data-current_address="'.$currentAddress.'"
                            data-current_mobile_number="'.$row->worker->mobile_number.'"
                            data-requested_address="'.$requestedAddress.'"
                            data-requested_mobile_number="'.$requestedData['mobile_number'].'">
                                <i class="fs-2 las la-eye"></i>
                            </a>',
                        'action' => $this->action($row['id']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($getAddressRequest),
                'recordsFiltered'   => count($getAddressRequest),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
