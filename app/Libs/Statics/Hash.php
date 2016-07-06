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

abstract class Hash {

    public static function make($string) {
        return password_hash($string, PASSWORD_BCRYPT);
    }

    public static function unique($length = 70) {
        return substr(base64_encode(self::make(uniqid(rand(), true))), 10, $length);
    }

    public static function rehash($value) {
        if (password_needs_rehash($value, PASSWORD_BCRYPT)) {
            return password_hash($value, PASSWORD_BCRYPT);
        }
        return $value;
    }

    public static function match($string, $hash) {
        return password_verify($string, $hash);
        ;
    }

}
