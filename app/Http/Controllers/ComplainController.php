<?php

namespace App\Http\Controllers;

use App\Classes\User;
use App\Libs\Concretes\Controller;
use App\Libs\Statics\Request;
use App\Libs\Statics\Response;
use App\Models\ComplainModel;
use App\Models\UserModel;
use function goBack;

class ComplainController extends Controller {

    public function insert() {
        $complain = [
            'user_id' => UserModel::first('hash = ?', [User::getHash()])->id,
            'diagnostic' => Request::getParam('diagnostic'),
            'description' => Request::getParam('description'),
        ];
        ComplainModel::insert($complain);
        goBack();
    }

    public function desc($id) {
        Response::json(ComplainModel::id($id));
    }

    public function delete($id) {
        Response::json(ComplainModel::delete('id = ?', [$id]));
    }

}
