<?php

namespace App\Http\Controllers\Accommodation;

use App\Http\Controllers\Controller;
use App\Models\Accommodation\Accommodation;
use App\Models\Group\CostCentre;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AccommodationController extends Controller
{
    use JsonResponse;
    public function index() {
        return view('accommodations.dis_accommodation');
    }

    public function getAccommodation(Request $request) {
        try {
            $status = $request->input('status');
            if (in_array($status, ['Active','Archived'])) {
                $accommodation = Accommodation::query()->where('status', $status)->orderBy('id', 'desc')->get();
            } elseif ($status === 'All') {
                $accommodation = Accommodation::query()->orderBy('id', 'desc')->get();
            } else {
                $accommodation = [];
            }

            $array  = [];
            if ($accommodation) {
                foreach ($accommodation as $row) {
                    $costCentres = CostCentre::query()->whereIn('id', explode(', ', $row['cost_center']))->get()->pluck('short_code')->toArray();
                    $array[] = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'city' => $row['city'],
                        'status' => $row['status'],
                        'cost_center' => implode(', ', $costCentres),
                        'contact_name' => $row['contact_name'],
                        'telephone' => $row['contact_number'],
                        'action' => $this->action($row['id'], $row['status']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($accommodation),
                'recordsFiltered'   => count($accommodation),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function action($id, $status) {
        $action = '';
        if (Auth::user()['user_type'] == 'Admin') {
            if ($status == 'Active') {
                $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="archive_accommodation" data-id="' . $id . '">
                            <i class="fs-2 las la-archive"></i>
                            </a>';
            } elseif ($status == 'Archived') {
                $action .= '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm me-1" id="un_archive_accommodation" data-id="' . $id . '">
                            <i class="fs-2 las la-undo"></i>
                        </a>';
            }
        }

        $action .= '<a href="'.url('view-accommodation/'.$id).'" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="view_accommodation">
                      <i class="fs-2 las la-arrow-right"></i>
                </a>';

        return $action;
    }

    public function createAccommodation() {
        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('accommodations.add_accommodation', compact('costCentre'));
    }

    public function storeAccommodation(Request $request) {
        try {
            $validator = Validator::make($request->input(), [
                'name' => 'required|string',
                'number_of_spaces' => 'required|integer|min:1',
                'description' => 'required',
                'cost_center' => 'required|array|min:1',
                'address_line_one' => 'required|string|max:255',
                'postcode' => 'required|string|max:15',
                'country' => 'required|string|max:25',
                'city' => 'required|string|max:25',
                'what_three_words_locator' => 'required|string|max:100',
                'contact_name' => 'required|string|max:100',
                'contact_number' => 'required|string|max:20',
            ]);

            if ($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }
            $data = $request->only([
                'name', 'number_of_spaces', 'description', 'address_line_one', 'address_line_two', 'postcode', 'country', 'city', 'what_three_words_locator', 'contact_name', 'contact_number'
            ]);
            $data['cost_center'] = implode(', ', $request->input('cost_center'));

            if (!empty($request->store_id) && $request->store_id != 0) {
                $accommodation = Accommodation::query()->where('id', $request->input('store_id'));

                if (!$accommodation) {
                    return self::responseWithError('Accommodation not found.');
                }

                $accommodation->update($data);
                return self::responseWithSuccess('Accommodation successfully updated.');

            } else {
                Accommodation::query()->create($data);
                return self::responseWithSuccess('Accommodation successfully created.');
            }
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function viewAccommodation($id) {
        $accommodation = Accommodation::query()->where('id', $id)->first();
        $costCentre = CostCentre::query()->orderBy('short_code', 'asc')->get();
        return view('accommodations.view_accommodation', compact(['accommodation', 'costCentre']));
    }

    public function archiveAccommodation(Request $request) {
        try {
            $accommodation = Accommodation::query()->where('id', $request->input('id'));

            if (!$accommodation) {
                return self::responseWithError('Accommodation not found.');
            }

            $status = $request->input('status');
            if (!in_array($status, ['Archived', 'Active'])) {
                return self::responseWithError('Invalid status passed.');
            }

            $accommodation->update([
                'status' => $status
            ]);
            return self::responseWithSuccess('Accommodation successfully '. strtolower($status).'.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
