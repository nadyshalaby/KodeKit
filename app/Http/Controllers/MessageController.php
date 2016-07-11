<?php

namespace App\Http\Controllers;

use App\Libs\Concretes\Controller;
use App\Libs\Statics\Response;
use App\Models\MessageModel;

class MessageController extends Controller {

    public function seen($id) {
        Response::json(MessageModel::update(['viewed' => 1], "id = ?", [$id]));
    }

    public function report($id) {
        $msg = MessageModel::id($id);
        if ($msg) {
            Response::download(path($msg->report));
        } else {
            goBack();
        }
    }

    public function delete($id) {
        $msg = MessageModel::id($id);
        if (!empty($msg->report)) {
            @unlink(path($msg->report));
        }
        Response::json(MessageModel::delete('id = ?', [$id]));
    }

}
