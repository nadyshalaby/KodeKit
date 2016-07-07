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

use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use function getClassBaseName;
use function multiexplode;

abstract class View {

    private static $_twig = null;

    public static function show($path, $params = []) {
        foreach ($params as $key => $value) {
            if (is_numeric($key)) {
                continue;
            }
            ${$key} = $value;
        }
        $pagename = multiexplode(['.', '/', '>', '|'], $path)[0];
        require_once Url::view($path);
    }

    /**
     * Load the passed view with the optional args
     * @param string $path the path to the view to be rendered
     * @param array $args arguments to be passed with the view
     * @return type
     */
    public static function twig($path, $args = []) {
        if (is_null(self::$_twig)) {
            $loader = new Twig_Loader_Filesystem(__DIR__ . "/../../../resources/views/");
            self::$_twig = new Twig_Environment($loader,  Config::twig('config'));

            // load functions of defined classes into Twig Environment
            self::twigStaticFunctions();
            // load functions of defined callables into Twig Environment
            self::twigCallableFunctions();
            // load defined filters into Twig Environment
            self::twigFilters();
        }
        return self::$_twig->render($path, $args);
    }

    /**
     * Loads the Functions of the defined classes into the Twig Environment
     */
    private static function twigStaticFunctions() {
        $classes = Config::twig('static_functions');
        foreach ($classes as $key => $cls) {
            $methods = get_class_methods($cls);
            foreach ($methods as $name) {
                $newname = strtolower(getClassBaseName($cls)) . '_' . $name;
                self::$_twig->addFunction(new Twig_SimpleFunction($newname, [$cls, $name]));
            }
        }
    }

    /**
     * Loads the Functions of the defined callables into the Twig Environment
     */
    private static function twigCallableFunctions() {
        $classes = Config::twig('callable_functions');
        foreach ($classes as $key => $cls) {
            if (is_string($key) && is_callable($cls)) {
                self::$_twig->addFunction(new Twig_SimpleFunction($key, $cls));
            }
        }
    }

    /**
     * Loads the defined Filters into the Twig Environment
     */
    private static function twigFilters() {
        $filters = Config::twig('filters');
        foreach ($filters as $name => $callable) {
            self::$_twig->addFilter(new Twig_SimpleFilter($name, $callable));
        }
    }

}
