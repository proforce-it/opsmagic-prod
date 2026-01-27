<?php

namespace App\Http\Controllers\Api\V1\Workers\RTWs;

use App\Http\Controllers\Controller;
use App\Models\Worker\RightsToWork;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;

class RTWsController extends Controller
{
    use JsonResponse;
    public function index(Request $request) {
        try {
            $workerId = auth('api')->id();
            $RTWs = RightsToWork::query()->where('worker_id', $workerId)
                ->orderBy('end_date', 'desc')
                ->get()
                ->map(function ($rtw) {
                    $rtw->is_expired = now()->gt($rtw->end_date) ? true : false;
                    return $rtw;
                });
            return self::responseWithSuccess('RTWs details fetched successfully.', $RTWs);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
