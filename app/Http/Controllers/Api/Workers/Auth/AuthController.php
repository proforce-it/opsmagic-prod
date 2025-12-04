<?php

namespace App\Http\Controllers\Api\Workers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use JsonResponse;
    public function index(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => [
                'bail',
                'required',
                'email',
                'exists:workers,email_address',
                function ($attribute, $value, $fail) {
                    $worker = Worker::query()->where('email_address', $value)->first();

                    if ($worker && empty($worker->email_verified_at)) {
                        $fail('Your email is not verified.');
                    }
                },
            ],
            'password' => 'required',

        ], [
            'email.exists' => 'This email does not exist in our system.',
            'email.email' => 'The email format is invalid.',
        ]);

        if ($validator->fails()) {
            return self::apiValidationError($validator->errors()->messages());
        }

        if (!$token = auth('api')->attempt([
            'email_address' => $request->input('email'),
            'password' => $request->input('password'),
        ])) {
            return self::responseWithError('Email or password is invalid.');
        }

        $worker = auth('api')->user();
        $worker->token = $token;
        $worker->save();

        return self::responseWithSuccess('You are successfully logged in.', [
            'token' => $token
        ]);
    }
}
