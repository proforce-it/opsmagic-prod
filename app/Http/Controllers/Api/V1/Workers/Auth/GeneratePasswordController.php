<?php

namespace App\Http\Controllers\Api\V1\Workers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Worker\Worker;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GeneratePasswordController extends Controller
{
    use JsonResponse;
    public function generatePasswordLink(Request $request) {
        try {
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
                ],],
                [
                    'email.exists' => 'This email does not exist in our system.',
                    'email.email' => 'The email format is invalid.',
                ]
            );

            if ($validator->fails()) {
                return self::apiValidationError($validator->errors()->messages());
            }

            $worker = Worker::query()->where('email_address', $request->input('email'))->first();
            $token = Str::random(64);
            DB::table('password_resets')->updateOrInsert(
                ['email' => $worker->email_address],
                [
                    'email' => $worker->email_address,
                    'token' => $token,
                    'created_at' => Carbon::now(),
                ]
            );

            $generatingPasswordUrl = url("/reset-worker-password?token={$token}&email={$worker['email_address']}");
            Mail::send('api.workers.emails.password_reset', [
                'worker_name' => trim($worker->first_name.' '.$worker->middle_name.' '.$worker->last_name),
                'url' => $generatingPasswordUrl
            ], function ($message) use ($worker) {
                $message->to($worker->email_address);
                $message->subject('Password generating Request');
            });

            return self::responseWithSuccess('Password generating link sent to your email.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
