<?php

/**
 * This file is part of kodekit framework
 * 
 * @copyright (c) 2015-2016, nady shalaby
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Classes;

use App\Libs\Statics\Config;
use App\Libs\Statics\Cookie;
use App\Libs\Statics\Session;
use App\Models\UserModel;

class User {

    private $_sessionName = '',
            $_cookieName = '',
            $_rememberMe;

    public function __construct($remember_me = '') {
        $this->_sessionName = Config::extra('session.remember_me');
        $this->_cookieName = Config::extra('cookie.remember_me');
        $this->_rememberMe = $remember_me;
    }

    public function login($remember = true) {
        if (!empty($this->_rememberMe)) {
            Session::put($this->_sessionName, $this->_rememberMe);
            if ($remember) {
                Cookie::put($this->_cookieName, $this->_rememberMe, Config::extra('cookie.remember_me_expiry'));
            }
            return TRUE;
        }
        return FALSE;
    }

    public function isLoggedIn() {
        if (Session::has($this->_sessionName)) {
            return true;
        } else if (Cookie::has($this->_cookieName)) {
            Session::put($this->_sessionName, Cookie::get($this->_cookieName));
            Cookie::put($this->_cookieName, Cookie::get($this->_cookieName), Config::extra('cookie.remember_me_expiry'));
            return true;
        }
        return false;
    }

    public function logout() {
        Session::delete($this->_sessionName);
        Cookie::delete($this->_cookieName);
    }

    public static function getHash() {
        return Session::get(Config::extra('session.remember_me'));
    }

    public static function getData() {
        return UserModel::first('hash = ?', [self::getHash()]);
    }

}
