<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Models\Group\CostCentre;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CostCentreController extends Controller
{
    use JsonResponse;
    public function index() {
        return view('groups.dis_cost_centres');
    }

    public function getCostCentre(Request $request) {
        try {
            if ($request->input('status') == 'Archived') {
                $query = CostCentre::onlyTrashed();
            } else if($request->input('status') == 'All') {
                $query = CostCentre::withTrashed();
            } else {
                $query = CostCentre::query();
            }
            $costCentre = $query->get();;
            $array  = [];
            if ($costCentre) {
                foreach ($costCentre as $row) {
                    $array[] = [
                        'name'  => $row['name'],
                        'short_code' => $row['short_code'],
                        'action'    => $this->action($row['id'], $row['deleted_at'], $row['name']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($costCentre),
                'recordsFiltered'   => count($costCentre),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function action($id, $deleted_at, $name) {
        if (is_null($deleted_at)) {
            $action = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1 archive_un_archived_cost_centre" data-cost-centre_id="'.$id.'" data-status="archived">
                    <i class="fs-2 las la-archive"></i>
                </a>

                <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="edit_cost_centre" data-cost-centre_id="'.$id.'"  data-cost-centre-name="'.htmlspecialchars($name, ENT_QUOTES, 'UTF-8').'">
                    <i class="fs-2 las la-edit"></i>
                </a>';
        } else {
            $action = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1 archive_un_archived_cost_centre" data-cost-centre_id="'.$id.'" data-status="unarchived">
                <i class="fs-2 las la-undo"></i>
            </a>';
        }

        return $action;
    }

    public function storeCostCentreAction(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:cost_centres,name',
            'short_code' => 'required|max:4|unique:cost_centres,short_code',
        ]);

        if ($validator->errors()->messages())
            return self::validationError($validator->errors()->messages());

        $params = $request->input();
        CostCentre::query()->create([
            'name' => $params['name'],
            'short_code' => $params['short_code'],
        ]);

        return self::responseWithSuccess('Cost centre successfully created.');
    }

    public function getSingleCostCentre($id) {
        try {
            $costCentre = CostCentre::query()->where('id', $id)->first();
            if (!$costCentre) {
                return self::responseWithError('Cost centre not found, please try again later.');
            }

            return self::responseWithSuccess('Cost centre details',  [
                'cost_centre_details' => $costCentre
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function editCostCentreAction(Request $request) {
        $validator = Validator::make($request->all(), [
            'edit_cost_centre_id' => 'required|exists:cost_centres,id',
            'edit_name' => [
                'required',
                Rule::unique('cost_centres', 'name')->ignore($request->input('edit_cost_centre_id')),
            ],
            'edit_short_code' => [
                'required',
                'max:4',
                Rule::unique('cost_centres', 'short_code')->ignore($request->input('edit_cost_centre_id')),
            ],
        ]);

        if ($validator->errors()->messages()) {
            return self::validationError($validator->errors()->messages());
        }

        $params = $request->input();
        $costCentre = CostCentre::query()->where('id', $params['edit_cost_centre_id'])->first();
        if (!$costCentre) {
            return self::responseWithError('Cost centre not found, please try again later.');
        }

        $costCentre->update([
            'name' => $params['edit_name'],
            'short_code' => $params['edit_short_code'],
        ]);

        return self::responseWithSuccess('Cost centre details successfully updated.');
    }

    public function deleteCostCentreAction(Request $request) {
        try {
            $params = $request->input();
            $costCentre = CostCentre::withTrashed()->where('id', $params['id'])->first();
            if(!$costCentre) {
                return self::responseWithError('Cost centre details not found, please try again.');
            }

            if ($params['status'] == 'archived') {
                CostCentre::query()->where('id', $params['id'])->delete();
                $message = 'Cost centre successfully archived.';
            } else {
                CostCentre::onlyTrashed()->where('id', $params['id'])->update([
                    'deleted_at' => null
                ]);
                $message = 'Cost centre successfully un-archived.';
            }
            return self::responseWithSuccess($message);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

}
