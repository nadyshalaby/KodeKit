<?php

/**
 * This file is part of kodekit framework
 * 
 * @copyright (c) 2015-2016, nady shalaby
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Libs\Statics;

abstract class Token {

    public static function make() {
        return Session::put('_token', trim(base64_encode(md5(uniqid(rand()))), '='));
    }

    public static function csrf() {
        return Session::get('_token');
    }

    public static function input($new = true) {
        $token = ($new) ? Session::put('_token', trim(base64_encode(md5(uniqid(rand()))), '=')) : self::csrf();
        return '<input type="hidden" name="_token" value="' . $token . '"/>';
    }

    public static function put($new = true) {
        $token = ($new) ? Session::put('_token', trim(base64_encode(md5(uniqid(rand()))), '=')) : self::csrf();
        return '<input type="hidden" name="_token" value="' . $token . '"/>'
                . '<input type="hidden" name="_method" value="PUT"/>';
    }

    public static function delete($new = true) {
        $token = ($new) ? Session::put('_token', trim(base64_encode(md5(uniqid(rand()))), '=')) : self::csrf();
        return '<input type="hidden" name="_token" value="' . $token . '"/>'
                . '<input type="hidden" name="_method" value="DELETE"/>';
    }

    public static function match($token) {

        $csrf_token = '_token';

        if (Session::has($csrf_token) && strcmp($token, Session::get($csrf_token)) == 0) {
            Session::delete($csrf_token);
            return true;
        }

        return false;
    }

}
