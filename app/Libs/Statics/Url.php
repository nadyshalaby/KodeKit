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

use App\Libs\Concretes\Router;
use function multiexplode;

abstract class Url {
    
    const RES  = "resources";
    const PUB  = "public";
    const PUB_IMG  = "public/images";
    const PUB_JS  = "public/";
    const PUB_CSS  = "public";
    const PUB_FONT  = "public";
    const VIEW  = "resources";

    public static function app() {
        return Config::app('url.app');
    }

    public static function css($file) {
        return self::app() . "public/css/$file.css";
    }

    public static function img($file) {
        return self::app() . "public/images/$file";
    }

    public static function js($file) {
        return self::app() . "public/js/$file.js";
    }

    public static function pub($file) {
        return self::app() . "public/$file";
    }

    public static function res($file) {
        return self::app() . "resources/$file";
    }

    public static function path($file) {
        $chunks = multiexplode(['.','/', '>', '|'], $file);
        $file = implode('/', $chunks);
        $path = __DIR__ . "/../../../$file";
        if(is_dir($path)){
            return $path;
        }else{
           $file_name = implode('.',array_slice($chunks, -2,2));
           $file_parent = implode('/',array_slice($chunks, 0,-2));
           $path = __DIR__ . "/../../../$file_parent/$file_name";
           if(is_file($path)){
               return $path;
           }
        }
        return null;
    }

    public static function route($name, $params = []) {
        return self::app() . trim(Router::getUrl($name, $params), '/');
    }

}
