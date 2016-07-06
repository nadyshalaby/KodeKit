<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Middlewares;

use App\Libs\Statics\Request;
use App\Libs\Statics\Session;
use App\Libs\Statics\Validation;
use function goBack;

class SignupMiddleware {

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
                'required' => true,
                'field' => 'email',
                'unique' => 'users',
                'title' => 'E-mail'
            ],
            'pass' => [
                'required' => true,
                'field' => 'nr_password',
                'min' => 8,
                'title' => 'Password'
            ],
            'tel' => [
                'required' => true,
                'field' => 'phone',
                'unique' => 'users',
                'title' => 'Telephone'
            ],
            'mobile' => [
                'required' => true,
                'field' => 'phone',
                'unique' => 'users',
                'title' => 'Mobile'
            ],
            'repass' => [
                'required' => true,
                'matches' => 'pass',
                'title' => 'Re-password'
            ],
        ]);
        if (Validation::passed()) {
            return $next();
        } else {
            $msgs = Validation::getAllErrorMsgs();
            $str = '';
            foreach ($msgs as $msg) {
                $str .= '<li><span class="msg-error" >Error: </span> ' . $msg . '</li>';
            }
            Session::flash('msg', $str);
            Session::flash('data', $user_data);
            goBack();
        }
    }

}
