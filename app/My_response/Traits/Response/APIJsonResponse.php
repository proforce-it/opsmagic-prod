<?php

namespace App\My_response\Traits\Response;

trait APIJsonResponse
{
    public function responseWithSuccess($data, $message = 'Hello world') {
        return response()->json([
            'response'  => 'success',
            'message'   => $message,
            'results'   => $data
        ]);
    }

    public function responseWithError($data, $message = 'Hello world') {
        return response()->json([
            'response'  => 'error',
            'message'   => $message,
            'results'   => $data
        ]);
    }

    public function apiErrorResponse($data = [], $message = 'Hello world')
    {
        return response()->json([
            "code"      => 500,
            "message"   => $message,
            "data"      => $data
        ], 500);
    }

    public function apiSuccessResponse($message, $data = [])
    {
        return response()->json([
            "code"      => 200,
            "message"   => $message,
            "data"      => $data
        ], 200);
    }
}
