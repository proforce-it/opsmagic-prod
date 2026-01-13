<?php

namespace App\Http\Controllers\Payroll;

use App\Exports\NewStarterExport;
use App\Http\Controllers\Controller;
use App\Models\Group\CostCentre;
use App\Models\Timesheet\Timesheet;
use App\My_response\Traits\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class NewStartersController extends Controller
{
    use JsonResponse;
    public function index(){
        $costCentre = CostCentre::query()->get();
        return view('new_starters.dis_report', compact('costCentre'));
    }

    public function newStartersAction(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after:start_date',
            ]);

            if ($validator->fails()) {
                return self::validationError($validator->errors()->messages());
            }

            $costCenter = $request->input('cost_center') ?? 'Any';
            $startDate = Carbon::parse($request->input('start_date'))->format('Y-m-d');
            $endDate = Carbon::parse($request->input('end_date'))->format('Y-m-d');

            $workers = Timesheet::query()
                ->select( 'worker_id', DB::raw('MIN(date) as first_working_date'))
                ->when($costCenter != 'Any', function ($query) use ($costCenter) {
                    $query->whereHas('worker_all_details.worker_cost_centres_with_name', function ($subQuery) use ($costCenter) {
                        $subQuery->where('cost_center_id', $costCenter);
                    });
                })
                ->groupBy('worker_id')
                ->havingBetween('first_working_date', [$startDate, $endDate])
                ->with('worker_all_details')
                ->get()
                ->map(function($item) {
                    return [
                        'worker_id' => $item->worker_id,
                        'first_working_date' => $item->first_working_date,
                        'worker_details' => $item->worker_all_details,
                    ];
                });

            return self::responseWithSuccess('New starter export.', [
                'fileName' => 'new_starters_report_' . now()->format('dmY_Hi') . '.csv',
                'csv' => Excel::raw(new NewStarterExport($workers), \Maatwebsite\Excel\Excel::CSV)
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
