<?php

namespace App\Http\Controllers\PickupPoint;

use App\Http\Controllers\Controller;
use App\Models\Group\CostCentre;
use App\Models\PickUpPoint\PickUpPoint;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PickupPointController extends Controller
{
    use JsonResponse;
    public function index() {
        return view('pickup_point.dis_pickup_point');
    }

    public function getPickUpPoint(Request $request) {
        try {
            $status = $request->input('status');
            if (in_array($status, ['Active','Archived'])) {
                $pickup_point = PickUpPoint::query()->where('status', $status)->orderBy('id', 'desc')->get();
            } elseif ($status === 'All') {
                $pickup_point = PickUpPoint::query()->orderBy('id', 'desc')->get();
            } else {
                $pickup_point = [];
            }

            $array  = [];
            if ($pickup_point) {
                foreach ($pickup_point as $row) {
                    $costCentres = CostCentre::query()->whereIn('id', explode(', ', $row['cost_center']))->get()->pluck('short_code')->toArray();
                    $array[] = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'address_line_one' => $row['address_line_one'],
                        'cost_center' => implode(', ', $costCentres),
                        'what_three_words_locator' => $row['what_three_words_locator'],
                        'action' => $this->action($row['id'], $row['status']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($pickup_point),
                'recordsFiltered'   => count($pickup_point),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function action($id, $status)
    {
        $action = '';
        if (Auth::user()['user_type'] == 'Admin') {
            if ($status == 'Active') {
                $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="archive_pickup_point" data-id="' . $id . '">
                            <i class="fs-2 las la-archive"></i>
                            </a>';
            } elseif ($status == 'Archived') {
                $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm me-1" id="un_archive_pickup_point" data-id="' . $id . '">
                            <i class="fs-2 las la-undo"></i>
                        </a>';
            }
        }

        $action .= '<a href="' . url('view-pick-up-point/' . $id) . '" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_pickup_point">
            <i class="fs-2 las la-arrow-right"></i>
        </a>';

        return $action;
    }

    public function createPickUpPoint() {
        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('pickup_point.add_pickup_point', compact('costCentre'));
    }

    public function storePickUpPoint(Request $request) {
        try {
            $validator = Validator::make($request->input(), [
                'name' => 'required|string',
                'description' => 'required',
                'cost_center' => 'required|array|min:1',
                'address_line_one' => 'required|string|max:255',
                'postcode' => 'required|string|max:15',
                'country' => 'required|string|max:25',
                'city' => 'required|string|max:25',
                'what_three_words_locator' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }
            $data = $request->only([
                'name', 'description', 'address_line_one', 'address_line_two', 'postcode', 'country', 'city', 'what_three_words_locator'
            ]);
            $data['cost_center'] = implode(', ', $request->input('cost_center'));

            if (!empty($request->store_id) && $request->store_id != 0) {
                $accommodation = PickUpPoint::query()->where('id', $request->input('store_id'));

                if (!$accommodation) {
                    return self::responseWithError('Pick up point not found.');
                }

                $accommodation->update($data);
                return self::responseWithSuccess('Pick up point successfully updated.');

            } else {
                PickUpPoint::query()->create($data);
                return self::responseWithSuccess('Pick up point successfully created.');
            }
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function viewPickUpPoint($id) {
        $pickup_point = PickUpPoint::query()->where('id', $id)->first();
        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('pickup_point.view_pickup_point', compact('pickup_point', 'costCentre'));
    }

    public function archivePickUpPoint(Request $request) {
        try {
            $pickup_point = PickUpPoint::query()->where('id', $request->input('id'));

            if (!$pickup_point) {
                return self::responseWithError('Pick up point not found.');
            }

            $status = $request->input('status');
            if (!in_array($status, ['Archived', 'Active'])) {
                return self::responseWithError('Invalid status passed.');
            }

            $pickup_point->update([
                'status' => $status
            ]);
            return self::responseWithSuccess('Pick up point successfully '. strtolower($status).'.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
