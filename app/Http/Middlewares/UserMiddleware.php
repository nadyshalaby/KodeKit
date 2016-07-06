<?php

namespace App\Http\Middlewares;

use App\Classes\User;
use App\Libs\Statics\Session;
use App\Libs\Statics\Url;
use function goBack;

class UserMiddleware {

    public function control($next) {
        $u = new User;
        if ($u->isLoggedIn()) {
            return $next();
        } else {
            Session::flash("msg", '<li><span class="msg-warning">Warning: </span> Humm!... you want to cheat, please <a href="' . Url::route('login') . '">login</a> first and go back later!</li>');
            goBack();
        }
    }

}
