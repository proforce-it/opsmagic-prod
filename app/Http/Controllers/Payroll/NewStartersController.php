<?php

namespace App\Http\Controllers\Payroll;

use App\Exports\NewStarterExport;
use App\Http\Controllers\Controller;
use App\Models\Group\CostCentre;
use App\Models\Job\JobShiftWorker;
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
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $costCenter = $request->input('cost_center') ?? 'Any';
        $startDate = Carbon::parse($validated['start_date'])->format('Y-m-d');
        $endDate = Carbon::parse($validated['end_date'])->format('Y-m-d');

        $workers = JobShiftWorker::query()
            ->select( 'worker_id')
            ->whereNotNull('confirmed_at')
            ->whereNull('declined_at')
            ->whereNull('cancelled_at')
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->when($costCenter != 'Any', function ($query) use ($costCenter) {
                $query->whereHas('worker_all_details.worker_cost_centres_with_name', function ($subQuery) use ($costCenter) {
                    $subQuery->where('cost_center', $costCenter);
                });
            })
            ->whereDoesntHave('worker_all_details.worker_payroll_references', function ($query) {
                $query->whereNull('expires_on');
            })
            ->groupBy('worker_id')
            ->with([
                'worker_all_details',
                'worker_all_details.worker_cost_center'
            ])
            ->get()
            ->map(function($item) {
                return [
                    'worker_id' => $item->worker_id,
                    'first_working_date' => JobShiftWorker::query()->where('worker_id', $item->worker_id)
                        ->whereNotNull('confirmed_at')
                        ->whereNull('declined_at')
                        ->whereNull('cancelled_at')
                        ->orderBy('shift_date', 'asc')->pluck('shift_date')->first(),
                    'worker_all_details' => $item->worker_all_details,
                ];
            });

        return Excel::download(
            new NewStarterExport($workers),
            'new_starters_report_' . now()->format('dmY_Hi') . '.xls'
        );
    }
}
