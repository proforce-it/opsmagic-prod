<?php

namespace App\Http\Controllers\User;

use App\Helper\File\FileHelper;
use App\Http\Controllers\Controller;
use App\Mail\UserCreate;
use App\Models\Group\Team;
use App\Models\User;
use App\My_response\Traits\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use function Termwind\renderUsing;

class UserController extends Controller
{
    use JsonResponse;
    public function index() {
        $teamMembers = Team::query()->select(['id','name'])->get();
        return view('users.dis_user',compact('teamMembers'));
    }

    public function getUser(Request $request) {
        try {
            $user = User::query()
                //->whereNot('id', Auth::id())
                ->when(request('status') != null && request('status') != 'All', function ($q) { return $q->where('status', request('status')); })
                ->with('created_user')
                ->get();

            $array  = [];
            if ($user) {
                foreach ($user as $row) {
                    $array[] = [
                        'user_name' => $row['name'],
                        'status'    => $row['status'],
                        'permission'=> $row['user_type'],
                        'last_login'=> ($row['last_login']) ? date('d-m-Y - H:i:s', strtotime($row['last_login'])) : '',
                        'created_by'=> ($row['created_user']) ? '<span class="badge badge-secondary">'.$row['created_user']['name'].'</span>' : '-',
                        'created_at'=> '<span class="badge badge-secondary">'.date('d-m-Y - H:i:s', strtotime($row['created_at'])).'</span>',
                        'action'    => $this->action($row['id'], $row['status']),
                    ];
                }
            }
            return [
                'draw'              => 1,
                'recordsTotal'      => count($user),
                'recordsFiltered'   => count($user),
                'data'              => $array
            ];
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    private function action($id, $status) {
        if (Auth::id() != $id) {
            if ($status == 'Active') {
                $action = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1 archive_action" id="archive_user" data-user_id="'.$id.'" data-status="Archived" data-text="You want to archive this user!" data-btn_text="Yes, archive!">
                    <i class="fs-2 las la-archive"></i>
                </a>';
            } else {
                $action = '<a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1 archive_action" id="active_user" data-user_id="'.$id.'" data-status="Active" data-text="You want to active this user!" data-btn_text="Yes, active!">
                    <i class="fs-2 las la-undo"></i>
                </a>';
            }

            $action .= '
                <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" id="edit_user" data-user_id="'.$id.'" data-modal_title="Edit user" data-modal_btn="Update user">
                    <i class="fs-2 las la-edit"></i>
                </a>';

            return $action;
        } else {
            return '';
        }
    }

    public function getSingleUser($id) {
        try {
            $user = User::query()->where('id', $id)->first();
            if (!$user)
                return self::responseWithError('User not found, please try again later.');

            return self::responseWithSuccess('User details',  [
                'user_details' => $user
            ]);
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function storeUserAction(Request $request) {
        $validator = Validator::make($request->all(), [
            //'profile_pic'=> 'required|file|max:10240|mimes:png,jpg,jpeg',
            'name'      => 'required',
            'email'     => 'required|unique:users,email,'.$request->input('user_id'),
            'user_type' => 'required',
        ]);

        if ($validator->errors()->messages())
            return self::validationError($validator->errors()->messages());

        $params = $request->input();

        $profile_upload = ($request->hasFile('profile_pic'))
            ? FileHelper::file_upload($request->file('profile_pic'), 'workers/users')['file_name']
            : '';

        User::query()->create([
            'name'      => $params['name'],
            'email'     => $params['email'],
            'user_type' => $params['user_type'],
            'profile_pic' => $profile_upload,
            'created_by'=> Auth::id(),
            'team_id' => $params['team_members']
        ]);

        $userData = (object) [
            'name' => $params['name'],
            'link'  => url('user-password?email='.urlencode($params['email'])),
            'password'=> ''
        ];
        Mail::to($params['email'])->send(new UserCreate($userData));

        return self::responseWithSuccess('User successfully created.');
    }

    public function editUserAction(Request $request) {
        $validator = Validator::make($request->all(), [
            'edit_name'      => 'required',
            'edit_email'     => 'required|unique:users,email,'.$request->input('edit_user_id'),
            'edit_user_type' => 'required',
        ],[
            'edit_name.required'    => 'The name field is required.',
            'edit_email.required'   => 'The email field is required.',
            'edit_email.unique'     => 'The email has already been taken.',
            'edit_user_type.required' => 'The user type field is required.',
        ]);

        if ($validator->errors()->messages())
            return self::validationError($validator->errors()->messages());

        $params = $request->input();
        $user   = User::query()->where('id', $params['edit_user_id'])->first();
        if (!$user)
            return self::responseWithError('User not found, please try again later.');

        if ($request->hasFile('edit_profile_pic')) {
            if ($user['profile_pic']) {
                FileHelper::file_remove($user['profile_pic'], 'workers/users');
            }

            $profile_pic_upload = FileHelper::file_upload($request->file('edit_profile_pic'), 'workers/users');
            $file_name = $profile_pic_upload['file_name'];
        } else {
            if ($request->input('edit_user_avatar_remove') == 1) {
                if ($user['profile_pic']) {
                    FileHelper::file_remove($user['profile_pic'], 'workers/users');
                }

                $file_name = null;
            } else {
                $file_name = $user['profile_pic'];
            }
        }

        $user->update([
            'name'      => $params['edit_name'],
            'email'     => $params['edit_email'],
            'user_type' => $params['edit_user_type'],
            'profile_pic' => $file_name,
            'team_id' => $params['edit_team_members'],
        ]);

        return self::responseWithSuccess('User Details successfully updated.');
    }

    public function userPassword(Request $request) {
        $email = $request->get('email');

        $userData = User::query()->where('email', $email)->first();
        if (!$userData)
            return self::responseWithError('User not found, please contact to admin.');

        if ($userData->password != '') {
            return view('users.emails.user_create', compact('userData'));
        }
        return view('users.create_password', compact('email'));
    }

    public function updateUserPassword(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|confirmed',
                'email' => 'required',
            ],[
                'password.confirmed' => 'The password and confirmation password must match.',
            ]);

            if ($validator->errors()->messages())
                return self::validationError($validator->errors()->messages());

            $user = User::query()->where('email', $request->input('email'))->first();
            if (!$user)
                return self::responseWithError('User not found, please try again later.');

            $user->update([
                'password' => Hash::make($request->input('password'))
            ]);

            Auth::login($user);
            return self::responseWithSuccess('Password successfully created, now you can log in.');
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }

    public function updateUserStatus(Request $request) {
        try {
            User::query()->where('id', $request->input('user_id'))->update([
                'status' => $request->input('status')
            ]);
            return self::responseWithSuccess('User successfully '.strtolower($request->input('status')));
        } catch (\Exception $e) {
            return self::responseWithError($e->getMessage());
        }
    }
}
