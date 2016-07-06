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

abstract class Cookie {

    public static function put($name, $value, $expiry) {
        if (is_numeric($expiry)) {
            return (setcookie($name, $value, time() + $expiry, '/')) ? true : false;
        } else {
            return (setcookie($name, $value, strtotime($expiry), '/')) ? true : false;
        }
    }

    public static function delete($name) {
        return self::put($name, null, -1);
    }

    public static function has($name) {
        return isset($_COOKIE[$name]);
    }

    public static function get($name) {
        return (self::has($name)) ? $_COOKIE[$name] : null;
    }

}
