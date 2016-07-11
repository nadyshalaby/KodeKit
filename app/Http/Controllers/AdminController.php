<?php

namespace App\Http\Controllers;

use App\Classes\User;
use App\Libs\Concretes\Controller;
use App\Libs\Statics\Request;
use App\Libs\Statics\Response;
use App\Libs\Statics\Session;
use App\Libs\Statics\Url;
use App\Models\ComplainModel;
use App\Models\MessageModel;
use App\Models\PermissionModel;
use App\Models\UserModel;
use function goBack;

class AdminController extends Controller {

    public function desc($id) {
        Response::json(UserModel::id($id));
    }

    public function complainDelete() {
        $marks = Request::getParam('marks');

        $status = '';

        // if the complains selected
        if (count($marks)) {
            // loop through each complain and to delete
            foreach ($marks as $mark) {
                //confirm that the complain id is deleted
                if (ComplainModel::update(['status' => 'refused'], 'id = ?', [$mark])) {
                    $status .= 'Success: Deleting Complain #' . $mark . ' Successfully';
                } else {
                    $status .= 'Error: Deleting Complain #' . $mark . ' Failed';
                }
            }
            //if no complain selected
        } else {
            $status .= 'Error: Mark at least one complain to be delete';
        }
        Response::json($status);
    }

    public function reply() {
        $marks = Request::getParam('marks');
        $reply = Request::getParam('reply');
        $report = Request::getFile('report');

        $status = '';

        // if the complains selected and the replies sent
        if (count($marks) && !empty($reply)) {
            // loop through each complain and reply to 
            foreach ($marks as $mark) {
                //confirm that the complain id is exist
                if (!empty($complain = ComplainModel::id($mark))) {

                    $report_f = true;
                    // if the report uploaded
                    if ($report) {
                        $tmp = $report->tmp_name;
                        $file_parts = explode('.', $report->name);
                        //export the extension of the file
                        $report_ext = end($file_parts);
                        //remove the extension
                        array_pop($file_parts);
                        //get the file name
                        $report_name = implode('_', $file_parts);
                        // get the new file path 
                        $report = "resources/reports/{$report->name}";

                        // create unique name for the file
                        while (file_exists(path($report))) {
                            $report = $report_name . '_' . rand(0, 9999) . ".$report_ext";
                            $report = "resources/reports/{$report}";
                        }

                        $report_f = move_uploaded_file($tmp, path($report));
                    }

                    //building new message for reply
                    $msg = [
                        'complain_id' => $complain->id,
                        'user_id' => $complain->user_id,
                        'title' => "<b>[Reply to:] </b> {$complain->diagnostic} <b>[Num:] </b> {$complain->id} <b>[Date:] </b> {$complain->created_at}.",
                        'body' => $reply,
                        'report' => $report,
                    ];

                    // insert the message and update the complain status to replied
                    if ($report_f && MessageModel::insert($msg) && ComplainModel::update([
                                'status' => 'replied'
                                    ], 'id = ?', [$complain->id])) {
                        $status .= '<li><span class="msg-success">Success: </span> Replied to Complain #' . $complain->id . ' Successfully</li>';
                    } else {
                        $status .= '<li><span class="msg-error">Error: </span> Reply to Complain #' . $complain->id . ' Failed</li>';
                    }
                }
            }
            //if no complain selected or empty reply
        } else {
            $status .= '<li><span class="msg-error">Error: </span> Mark at least one complain to be replied and couldn\'t reply with empty</li>';
        }
        Session::flash("msg", $status);
        goBack();
    }

    public function delete($id) {
        $current = User::getData();
        $admin = PermissionModel::findBy([
                    'user_id' => $current->id,
                    'permission' => 'admin'
        ]);
        $userFlag = $perFlag = $msgFlag = $compFlag = FALSE;

        if ($current->id != $id && $admin) {
            $avatar = UserModel::id($id)->avatar;
            if (!empty($avatar)) {
                @unlink(Url::resource($avatar));
            }
            $userFlag = UserModel::delete('id = ?', [$id]);
            $perFlag = PermissionModel::delete('user_id = ?', [$id]);
            $msgFlag = MessageModel::delete('user_id = ?', [$id]);
            $compFlag = ComplainModel::delete('user_id = ?', [$id]);
        }
        Response::json(['status' => ($userFlag && $perFlag && $msgFlag && $compFlag)]);
    }

}
