<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Middlewares;

use App\Classes\User;
use App\Libs\Statics\Hash;
use App\Libs\Statics\Request;
use App\Libs\Statics\Session;
use App\Libs\Statics\Url;
use App\Libs\Statics\Validation;
use App\Models\UserModel;
use function goBack;
use function scanImageToPng;

class AccountUpdateMiddleware {

    function control($next) {
        $user_data = Request::getALlParams();
        Validation::check($user_data, [
            'name' => [
                'required' => true,
                'unicode_space' => true,
                'min' => 2,
                'title' => 'Name'
            ],
            'email' => [
                'field' => 'email',
                'title' => 'E-mail'
            ],
            'pass' => [
                'required' => true,
                'field' => 'nr_password',
                'min' => 8,
                'title' => 'Password'
            ],
            'newpass' => [
                'field' => 'nr_password',
                'min' => 8,
                'title' => 'New Password'
            ],
            'repass' => [
                'matches' => 'newpass',
                'title' => 'Re-password'
            ],
            'tel' => [
                'field' => 'phone',
                'title' => 'Telephone'
            ],
            'mobile' => [
                'field' => 'phone',
                'title' => 'Mobile'
            ],
        ]);

        $avatar = Request::getFile('avatar');
        $str = '';



        if (Validation::passed()) {
            // grapping the current user data
            $user = User::getData();

            // password check
            if (Hash::match(Request::getParam('pass'), $user->pass)) {
                // if the avatar is set it will be tested
                $avatarFlag = true;
                if (!empty($avatar)) {
                    $avatarFlag = ($avatar->size <= 100000 && scanImageToPng($avatar->tmp_name, Url::resource("images/{$avatar->name}")));
                    if (!$avatarFlag) {
                        $str .= '<li><span class="msg-error" >Error: </span> The Avatar must be an image and less that 10 MB</li>';
                    }
                }

                //if the email changed it will be tested 
                $email = Request::getParam('email');
                $emailFlag = true;
                if ($user->email != $email && UserModel::findBy([
                            'email' => $email
                        ])) {
                    $emailFlag = false;
                    $str .= '<li><span class="msg-error" >Error: </span> The Email already Exists choose another one</li>';
                }

                //if the telephone changed it will be tested 
                $tel = Request::getParam('tel');
                $telFlag = true;
                if ($user->tel != $tel && UserModel::findBy([
                            'tel' => $tel
                        ])) {
                    $telFlag = false;
                    $str .= '<li><span class="msg-error" >Error: </span> The Telephone already Exists choose another one</li>';
                }

                //if the mobile changed it will be tested 
                $mobile = Request::getParam('mobile');
                $mobileFlag = true;
                if ($user->mobile != $mobile && UserModel::findBy([
                            'mobile' => $mobile
                        ])) {
                    $mobileFlag = false;
                    $str .= '<li><span class="msg-error" >Error: </span> The Mobile already Exists choose another one</li>';
                }

                // if the avatar test and the email test and the mobile test and the telephone test are passed,
                //  move to next step
                if ($avatarFlag && $emailFlag && $mobileFlag && $telFlag) {
                    return $next();
                }
            } else {
                $str .= '<li><span class="msg-error" >Error: </span> The Password doesn\'t match the current one</li>';
            }
        }

        $msgs = Validation::getAllErrorMsgs();
        if (count($msgs)) {
            foreach ($msgs as $msg) {
                $str .= '<li><span class="msg-error" >Error: </span> ' . $msg . '</li>';
            }
        }
        Session::flash('msg', $str);
        Session::flash('data', $user_data);
        goBack();
    }

}
