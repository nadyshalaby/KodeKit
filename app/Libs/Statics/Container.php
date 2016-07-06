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

use Exception;

abstract class Container {

    public static function __callStatic($method, $args) {
        $registered = include Config::container();

        if (in_array($method, array_keys($registered))) {
            if (is_callable($registered[$method])) {
                return call_user_func_array($registered[$method], $args);
            } else {
                return $registered[$method];
            }
        } else {
            throw new Exception("No registered method or object found", 1);
        }
    }

}
