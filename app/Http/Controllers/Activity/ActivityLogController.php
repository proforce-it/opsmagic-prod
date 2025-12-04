<?php

namespace App\Http\Controllers\Activity;

use App\Helper\Activity\ActivityLogs;
use App\Http\Controllers\Controller;
use App\Models\Activity\ActivityLog;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    use JsonResponse;
    public function index() {

    }

    public function listOfActivityLogs(Request $request) {
        //return ActivityLog::query()->when(request('log_for_id') != null, function ($q) { return $q->where('log_for_id', request('log_for_id')); })->when(request('menu_type') != null, function ($q) { return $q->where('menu_type', request('menu_type')); })->with('user_details')->pluck('field');
        try {
            $array = ActivityLogs::preparedDataTable(
                ActivityLog::query()->when(request('log_for_id') != null, function ($q) { return $q->where('log_for_id', request('log_for_id')); })
                    ->when(request('menu_type') != null, function ($q) { return $q->where('menu_type', request('menu_type')); })
                    ->with('user_details')
                    ->get()
            );
            return [
                'draw'              => 1,
                'recordsTotal'      => count($array),
                'recordsFiltered'   => count($array),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
