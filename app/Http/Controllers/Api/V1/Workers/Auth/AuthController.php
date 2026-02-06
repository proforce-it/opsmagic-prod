<?php

namespace App\Http\Controllers\Api\V1\Workers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Location\Country;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

        $worker = Worker::query()->where('email_address', $request->input('email'))->first();
        if (! $worker || ! Hash::check($request->input('password'), $worker->password)) {
            return self::responseWithError('Email or password is invalid.');
        }

        $token = $worker->createToken('worker-api-token')->accessToken;
        $worker->token = $token;
        $worker->fcm_token = $request->input('fcm_token');
        $worker->save();

        $country = Country::query()->where('id', $worker->nationality)->first();
        return self::responseWithSuccess('You are successfully logged in.', [
            'token' => $token,
            'worker_no' => $worker->worker_no,
            'title' => $worker->title,
            'first_name' => $worker->first_name,
            'middle_name' => $worker->middle_name,
            'last_name' => $worker->last_name,
            'date_of_birth' => date('d/m/Y', strtotime($worker->date_of_birth)),
            'gender' => $worker->gender,
            'email_address' => $worker->email_address,
            'mobile_number' => $worker->mobile_number,
            'permanent_country' => ($worker->same_as_current_address == 1) ? $worker->current_country : $worker->permanent_country,
            'nationality' => $country?->name,
            'profile_image' => $worker->profile_pic ? asset('workers/profile/' . $worker->profile_pic) : asset('assets/media/avatars/worker-square.png'),
        ]);
    }
}