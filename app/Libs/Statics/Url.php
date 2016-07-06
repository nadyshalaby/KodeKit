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

    public static function view($file) {
        $file = multiexplode(['.', '/', '>', '|'], $file);
        $file = implode('/', $file);
        return __DIR__ . "/../../../resourses/views/$file.php";
    }

    public static function resource($file) {
        $file = multiexplode(['/', '>', '|'], $file);
        $file = implode('/', $file);
        return __DIR__ . "/../../../resources/$file";
    }

    public static function route($name, $params = []) {
        return trim(self::app(), '/') . Router::getUrl($name, $params);
    }

}
