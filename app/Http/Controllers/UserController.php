<?php

namespace App\Http\Controllers;

use App\Classes\User;
use App\Libs\Concretes\Controller;
use App\Libs\Statics\Hash;
use App\Libs\Statics\Request;
use App\Libs\Statics\Response;
use App\Libs\Statics\Session;
use App\Libs\Statics\Url;
use App\Models\ComplainModel;
use App\Models\MessageModel;
use App\Models\PermissionModel;
use App\Models\UserModel;
use Carbon\Carbon;
use function goBack;
use function twig;

class UserController extends Controller {

    public function profile() {
        $user = User::getData();
        if (!empty($user)) {

            // setting a new properity for the user permission
            $permission = PermissionModel::first('user_id = ?', [$user->id]);
            $user->permission = $permission->permission;

            // if the user is admin then will fetch the not replied complains
            $requests = null;
            if ($permission->permission == 'admin') {

                $requests = ComplainModel::with([
                            'status' => 'bending'
                ]);

                if (count($requests)) {
                    foreach ($requests as $request) {
                        // fetching the data for the patient who made the complain
                        $request->patient = UserModel::id($request->user_id);
                    }
                }

                $requests_count = count($requests);
                return twig('profile-admin.html', [
                    'user' => $user,
                    'requests' => $requests,
                    'requests_count' => $requests_count
                ]);
            }

            $msgs = $complains = null;
            if ($permission->permission == 'normal') {
                // fetching the current user messages
                $msgs = MessageModel::with([
                            'user_id' => $user->id,
                ]);

                // fetching the current user complains
                $complains = ComplainModel::with([
                            'user_id' => $user->id
                ]);

                $msgs_count = count(MessageModel::with([
                            'user_id' => $user->id,
                            'viewed' => 0,
                ]));

                return twig('profile-user.html', [
                    'user' => $user,
                    'complains' => $complains,
                    'msgs' => $msgs,
                    'msgs_count' => $msgs_count,
                ]);
            }
        } else {
            Session::flash("msg", '<li><span class="msg-warning">Warning: </span> Humm!... you want to cheat, access denied</li>');
            goBack();
        }
    }

    public function delete() {
        $user = User::getData();

        if (!empty($user->avatar)) {
            @unlink(Url::resource($user->avatar));
        }

        $userFlag = UserModel::delete('id = ?', [$user->id]);
        $perFlag = PermissionModel::delete('user_id = ?', [$user->id]);
        $msgFlag = MessageModel::delete('user_id = ?', [$user->id]);
        $compFlag = ComplainModel::delete('user_id = ?', [$user->id]);
        $status = ($userFlag && $perFlag && $msgFlag && $compFlag);
        if ($status) {
            $u = new User;
            $u->logout();
        }
        Response::json(['status' => $status]);
    }

    public function update() {

        $user = User::getData();

        $name = Request::getParam('name');
        $email = Request::getParam('email');
        $newpass = Request::getParam('newpass');
        $tel = Request::getParam('tel');
        $address = Request::getParam('address');
        $mobile = Request::getParam('mobile');
        $gender = Request::getParam('gender');
        $avatar = '';
        if (Request::hasFile('avatar')) {
            $avatar = 'images/' . Request::getFile('avatar')->name;
        }

        if (empty($newpass)) {
            $newpass = Request::getParam('pass');
        }
        if (empty($avatar)) {
            $avatar = $user->avatar;
        }
        if (empty($address)) {
            $address = $user->address;
        }


        $user_columns = [
            'name' => $name,
            'email' => $email,
            'pass' => Hash::make($newpass),
            'mobile' => $mobile,
            'tel' => $tel,
            'gender' => $gender,
            'address' => $address,
            'avatar' => $avatar,
            'updated_at' => Carbon::now(),
        ];

        if (UserModel::update($user_columns, "id = ?", [User::getData()->id])) {
            goBack();
        } else {
            Response::error(401);
        }
    }

}
