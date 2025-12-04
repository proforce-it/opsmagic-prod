<?php

namespace App\Http\Controllers\Dashboards;

use App\Helper\File\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;


class ProfileController extends Controller
{
    use JsonResponse;

    public function index(Request $request){
        $user = User::query()->where('id',Auth::user()->id)->first();
        return view('dashboards.profile.my_profile',compact('user'));
    }
    public function updateDashboardTab(Request $request){

        try {
            $params = $request->input();

            $user = User::query()->select(['id', 'name', 'email'])->where('id', $params['user_id'])->first();

            if (!$user)
                return self::responseWithError('User not found, please try again.');

            User::query()->where('id', $params['user_id'])->update([
                'dashboard_tab' => $params['dashboard_tab'],
            ]);

            return self::responseWithSuccess('Dashboard tab successfully updated');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
    public function uploadUserProfilePic(Request $request) {
        try {
            $user = User::query()->select('id', 'profile_pic')->where('id', $request->input('user_id'))->first();
            if (!$user)
                return self::responseWithError('User not found, please try again later.');

            if ($request->hasFile('user_profile_pic')) {
                if ($user['profile_pic']) {
                    FileHelper::file_remove($user['profile_pic'], 'workers/users');
                }

                $upload = FileHelper::file_upload($request->file('user_profile_pic'), 'workers/users');
                $user->update([
                    'profile_pic' => $upload['file_name'],
                ]);

                return self::responseWithSuccess('Profile pic successfully uploaded.');
            } else {
                if ($request->input('avatar_remove') == 1) {
                    if ($user['profile_pic']) {
                        FileHelper::file_remove($user['profile_pic'], 'workers/users');
                    }

                    $user->update([
                        'profile_pic' => null,
                    ]);

                    return self::responseWithSuccess('Profile pic successfully removed.');
                } else {
                    return self::responseWithError('Please select a profile pic.');
                }
            }
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
    public function updatePasswordTab(){
        return view('dashboards.profile.partials.password_tab');
    }

    public function updateUserPassword(Request $request){
        try {
            $validator = Validator::make($request->input(), [
                'new_password'       => 'required',
                'confirm_password'   => 'required|same:new_password'
            ],[
                'new_password.required'      => 'Type new password field is required',
                'confirm_password.required'  => 'Retype new password field is required',
                'confirm_password.same'      => 'Passwords do not match',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $params = $request->input();

            $user = User::query()->where('id', $params['user_id'])->first();
            if (!$user)
                return self::responseWithError('User not found, please try again.');

            User::query()->where('id', $params['user_id'])->update([
                'password' => Hash::make($params['new_password']),
            ]);

            return self::responseWithSuccess('Password successfully updated.');
        } catch (Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
