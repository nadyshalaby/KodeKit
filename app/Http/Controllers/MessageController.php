<?php

namespace App\Http\Controllers;

use App\Libs\Concretes\Controller;
use App\Libs\Statics\Response;
use App\Models\MessageModel;

class MessageController extends Controller {

    
    public function seen($id) {
        Response::json(MessageModel::update(['viewed' => 1],"id = ?",[$id]));
    }

    
    public function delete($id) {
        Response::json(MessageModel::delete('id = ?', [$id]));
    }

}
