<?php

namespace App\Http\Middlewares;

use App\Classes\User;
use App\Models\PermissionModel;

class AdminMiddleware {

    public function control($next) {
        if (PermissionModel::findBy([
                    'user_id' => User::getData()->id,
                    'permission' => 'admin'
                ])) {
            return $next();
        } else {
            goBack();
        }
    }
}