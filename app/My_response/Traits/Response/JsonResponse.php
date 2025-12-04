<?php

namespace App\My_response\Traits\Response;

trait JsonResponse
{
    public static function responseWithSuccess($message = 'Hello world', $data = []) {
        return response()->json([
            'code'      => 200,
            'message'   => $message,
            'data'      => $data,
        ]);
    }

    public static function responseWithError($message = 'Hello world', $data = []) {
        return response()->json([
            'code'      => 500,
            'message'   => $message,
            'data'      => $data,
        ]);
    }

    public static function validationError($data = []) {
        return response()->json([
            'code'      => 422,
            'message'   => 'Validation error.',
            'data'      => $data,
        ]);
    }

    public static function apiValidationError($data = []) {
        return response()->json([
            'code'      => 422,
            'message'   => 'Validation error.',
            'data'      => $data,
        ], 422);
    }

    public static function unauthorizedError($message = 'Unauthorized') {
        return response()->json([
            'code'      => 401,
            'message'   => $message,
        ], 401);
    }
}
