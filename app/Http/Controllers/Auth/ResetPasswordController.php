<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\My_response\Traits\Response\JsonResponse;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    //use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    //protected $redirectTo = RouteServiceProvider::HOME;

    use JsonResponse;
    public function showResetForm(Request $request, $token) {
        return view('theme.auth.partials.reset_password', ['token' => $token, 'email' => $request->input('email')]);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'token'     => 'required',
            'email'     => 'required|email',
            'password'  => 'required|min:8|confirmed',
        ]);

        if ($validator->errors()->messages())
            return self::validationError($validator->errors()->messages());

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? self::responseWithSuccess(__($status))
            : self::responseWithError(__($status));
    }
}
