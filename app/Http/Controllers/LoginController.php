<?php

namespace App\Http\Controllers;

use App\Classes\User;
use App\Libs\Concretes\Controller;
use App\Libs\Statics\Hash;
use App\Libs\Statics\Request;
use App\Libs\Statics\Response;
use App\Libs\Statics\Session;
use App\Models\ComplainModel;
use App\Models\PermissionModel;
use App\Models\UserModel;
use function goBack;
use function redirect;
use function route;
use function twig;

class LoginController extends Controller {

    public function index() {
        return twig('login.html');
    }

    public function logout() {
        $u = new User;
        $u->logout();
        redirect(route('home'));
    }

    public function signin() {
        $email = Request::getParam('email');
        $pass = Request::getParam('pass');
        $remember = !empty(Request::getParam('remember'));
        $admin = !empty(Request::getParam('admin'));
        
        $user = UserModel::first('email = ?', [$email]);
        if ($user && Hash::match($pass, $user->pass)) {
            $permission = PermissionModel::first('user_id = ?' , [$user->id])->permission;
            
            // check permision type for the user
            if ($admin && $permission != 'admin') {
                Session::flash("msg", '<li><span class="msg-error">Error: </span> Ooops!... No admin found (wrong email or password ) , let\'s try one more time!</li>');
                Session::flash("data", Request::getALlParams());
                goBack();
                exit;
            }else if (!$admin && $permission == 'admin') {
                Session::flash("msg", '<li><span class="msg-error">Error: </span> Ooops!... No User found (wrong email or password ) , let\'s try one more time!</li>');
                Session::flash("data", Request::getALlParams());
                goBack();
                exit;
            }
            
            $u = new User($user->hash);
            $u->login($remember);
            redirect(route('user.profile'));
        } else {
            Session::flash("msg", '<li><span class="msg-warning">Warning: </span> Ooops!... wrong email or password, let\'s try one more time!</li>');
            Session::flash("data", Request::getALlParams());
            goBack();
        }
    }

    public function signup() {

        // grappping the registered user information via request
        $name = Request::getParam('name');
        $email = Request::getParam('email');
        $pass = Request::getParam('pass');
        $mobile = Request::getParam('mobile');
        $tel = Request::getParam('tel');
        $address = Request::getParam('address');
        $diagnostic = Request::getParam('diagnostic');
        $description = Request::getParam('description');
        $hash = UserModel::getHash();

        $user_columns = [
            'name' => $name,
            'email' => $email,
            'pass' => Hash::make($pass),
            'mobile' => $mobile,
            'tel' => $tel,
            'address' => $address,
            'hash' => $hash,
            'avatar' => '',
        ];


        // inserting new user 
        if (UserModel::insert($user_columns)) {

            // check if there is a complain then insert it
            $complain = [
                'user_id' => UserModel::lastId(),
                'diagnostic' => $diagnostic,
                'description' => $description,
            ];

            if (!empty($description) && !empty($diagnostic)) {
                ComplainModel::insert($complain);
            }

            // inserting permissions for the user as normal
            $permissions = [
                'user_id' => UserModel::lastId(),
            ];
            PermissionModel::insert($permissions);

            // login the user
            $u = new User($hash);
            $u->login();

            // redirect the user to profile page
            redirect(route('user.profile'));
        } else {
            Response::error(401);
        }
    }

    public function google() {
        $client = new Google_Client;
        $auth = new GoogleModel($client);
        if ($auth->updateUserInformation()) {
            $u = new User($auth->getUserRememberMe());
            $u->login();

            redirect(route('user', [
                'slug' => $auth->getUserSlug()
            ]));
            return;
        }
        Response::error(401);
    }

    public function facebook() {
        $fb = new FacebookModel();
        $fb->setLoginHelper();
        if ($fb->updateUserInformation()) {
            $u = new User($fb->getUserRememberMe());
            $u->login();

            redirect(route('user', [
                'slug' => $fb->getUserSlug()
            ]));
            return;
        }
        Response::error(401);
    }

}
