<?php

namespace App\Http\Middlewares;

use App\Classes\User;
use App\Libs\Statics\Session;
use App\Libs\Statics\Url;
use function goBack;

class LoginMiddleware {

    public function control($next) {
        $u = new User;
        if ($u->isLoggedIn()) {
            Session::flash("msg", '<li><span class="msg-warning">Warning: </span> You cannot login twice, please <a href="' . Url::route('logout') . '">Logout</a> first and try again!</li>');
            goBack();
        } else {
            return $next();
        }
    }

}
