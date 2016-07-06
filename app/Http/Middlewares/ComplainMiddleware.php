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

class ComplainMiddleware {

    function control($next) {
        $complain = Request::getALlParams();
        Validation::check($complain, [
            'description' => [
                'required' => true,
                'title' => 'Complain'
            ]
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
            Session::flash('data', $complain);
            goBack();
        }
    }

}
