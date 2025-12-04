<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    //use SendsPasswordResetEmails;

    use JsonResponse;
    public function showLinkRequestForm() {
        return view('theme.auth.partials.forget_password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'email' => 'required|email',
        ]);

        if ($validator->errors()->messages())
            return self::validationError($validator->errors()->messages());

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? self::responseWithSuccess(__($status))
            : self::responseWithError(__($status));
    }
}
