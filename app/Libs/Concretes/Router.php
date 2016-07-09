<?php

/**
 * This file is part of kodekit framework
 * 
 * @copyright (c) 2015-2016, nady shalaby
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Libs\Concretes;

use App\Libs\Concretes\Route;
use App\Libs\Exceptions\BadRequestException;
use App\Libs\Exceptions\TokenMissMatchException;
use App\Libs\Statics\Config;
use App\Libs\Statics\Request;
use App\Libs\Statics\Response;
use App\Libs\Statics\Token;
use Exception;
use function dd;
use function multiexplode;

class Router {

    private $url = '';
    private $routes = [];
    private $namedRoutes = [];
    private static $route;

    public function __construct() {
        $this->url = trim(strtolower(Request::getParam('url')), '/');
        if (empty($this->url)) {
            $this->url = '/';
        }
        self::$route = $this;
    }

    public function get($path, $callableOrOptions, $name = null, $with = [], $middleware = null, $token = false) {
        return $this->addRoute($path, $callableOrOptions, $name, 'GET', $with, $middleware, $token);
    }

    public function post($path, $callableOrOptions, $name = null, $with = [], $middleware = null, $token = true) {
        return $this->addRoute($path, $callableOrOptions, $name, 'POST', $with, $middleware, $token);
    }

    public function put($path, $callableOrOptions, $name = null, $with = [], $middleware = null, $token = true) {
        return $this->addRoute($path, $callableOrOptions, $name, 'PUT', $with, $middleware, $token);
    }

    public function ajax($path, $callableOrOptions, $name = null, $with = [], $middleware = null, $token = true) {
        return $this->addRoute($path, $callableOrOptions, $name, 'AJAX', $with, $middleware, $token);
    }

    public function delete($path, $callableOrOptions, $name = null, $with = [], $middleware = null, $token = true) {
        return $this->addRoute($path, $callableOrOptions, $name, 'DELETE', $with, $middleware, $token);
    }

    public function match(array $methods, $path, $callableOrOptions, $name = null, $with = [], $middleware = null, $token = true) {
        foreach ($methods as $value) {
            switch (strtoupper($value)) {
                case 'GET':
                $this->get($path, $callableOrOptions, $name, $with, $middleware, $token);
                break;
                case 'AJAX':
                $this->ajax($path, $callableOrOptions, $name, $with, $middleware, $token);
                break;
                case 'POST':
                $this->post($path, $callableOrOptions, $name, $with, $middleware, $token);

                break;
                case 'PUT':
                $this->put($path, $callableOrOptions, $name, $with, $middleware, $token);

                break;
                case 'DELETE':
                $this->delete($path, $callableOrOptions, $name, $with, $middleware, $token);

                break;
            }
        }
    }

    public function any($path, $callableOrOptions, $name = null, $with = [], $middleware = null, $token = true) {
        $this->get($path, $callableOrOptions, $name, $with, $middleware, $token);
        $this->ajax($path, $callableOrOptions, $name, $with, $middleware, $token);
        $this->post($path, $callableOrOptions, $name, $with, $middleware, $token);
        $this->put($path, $callableOrOptions, $name, $with, $middleware, $token);
        $this->delete($path, $callableOrOptions, $name, $with, $middleware, $token);
    }

    private function addRoute($path, $callableOrOptions, $name, $method, $with, $middleware, $token) {

        if (is_array($callableOrOptions)) {
            foreach ($callableOrOptions as $key => $value) {
                switch (strtolower($key)) {
                    case 'with':
                    $with = $value;
                    break;
                    case 'name':
                    $name = $value;
                    break;
                    case 'controller':
                    $callableOrOptions = $value;
                    break;
                    case 'middleware':
                    $middleware = $value;
                    break;
                    case 'token':
                    $token = $value;
                    break;
                }
            }
        }

        $optRoute = $this->optional($path, $callableOrOptions, $name, $method, $middleware, $token);
        $basRoute = $this->basic($path, $callableOrOptions, $name, $method, $middleware, $token);
        if (is_array($with)) {
            foreach ($with as $param => $pattern) {
                if ($optRoute) {
                    $optRoute->with($param, $pattern);
                }
                $basRoute->with($param, $pattern);
            }
        }
    }

    private function optional($path, $callableOrOptions, $name, $method, $middleware, $token) {
        if (strstr($path, '?')) {
            $path = str_replace('?', ':', $path);
            $route = new Route($path, $callableOrOptions, $middleware, $token);
            $this->routes[$method][] = $route;
            if ($name) {
                $this->namedRoutes[$name . '-opt'] = $path;
            }
            return $route;
        }
        return false;
    }

    private function basic($path, $callableOrOptions, $name, $method, $middleware, $token) {
        $path = preg_replace('#\?([\w]+[/]?)#', '', $path);
        $route = new Route($path, $callableOrOptions, $middleware, $token);
        $this->routes[$method][] = $route;
        if ($name) {
            $this->namedRoutes[$name] = $path;
        }
        return $route;
    }

    public static function getUrl($name, $params = []) {
        if (isset(self::$route->namedRoutes[$name])) {
            $path = self::$route->namedRoutes[$name];
            foreach ($params as $k => $v) {
                $path = str_replace(":$k", $v, $path);
            }
            return $path;
        }
        return '';
    }

    public function __destruct() {
        $app_middleware = Config::app('app_middleware');
        
        if (is_callable($app_middleware)) {
            return call_user_func($app_middleware, [$this, 'run']);
        } else if (!empty ($app_middleware) && is_string($app_middleware)) {
            $middleware = "App\\Http\\Middlewares\\{$app_middleware}Middleware";
            $middleware = new $middleware;
            return call_user_func([$middleware, 'control'], [$this, 'run']);
        } else {
            return $this->run();
        }
    }

    public function run() {
        try {
            if (isset($_SERVER['REQUEST_METHOD'])) {

                $request_method = $_SERVER['REQUEST_METHOD'];
                $request_method = (Request::isAjax()) ? 'AJAX' : $request_method;
                $inputFlag = Request::hasParam('_token');

                // check the request method if PUT, DELETE or POST 
                if ($request_method == 'POST') {                   
                    if (isset($_POST['_method'])) {
                        $request_method = $_POST['_method'];
                    }
                }
                // check if the request method not supported
                if(!in_array($request_method,['POST','GET','PUT','AJAX','DELETE'])){
                    throw new BadRequestException('Unauthorized: Access is denied, REQUEST_METHOD not found');
                }

                $res = null;
                // if any routes are set with the request method
                if (isset($this->routes[$request_method])) {
                    foreach ($this->routes[$request_method] as $route) {
                // find the route that matches the requested url
                        if ($route->equals($this->url)) {
                            // if the token field is set check the token
                            if ($route->token) {
                                $tokenFlag = Token::match(Request::getParam('_token'));
                                if (!$inputFlag || ($inputFlag && !$tokenFlag)) {
                                    throw new TokenMissMatchException('Unauthorized: Access is denied, Token Miss Match!');
                                    die('Token missmatch!');
                                }
                            }
                            // executes the requested route 
                            $res = $route->exec();
                            if (is_string($res)) {
                                echo $res;
                            } else if(!is_null($res)){
                                dd($res);
                            }
                            return;
                        }
                    }
                }
                Response::error(404);
            } else {
                throw new BadRequestException('Unauthorized: Access is denied, REQUEST_METHOD not found');
            }
        } catch (Exception $exc) {
            die($exc->getMessage() . ' please go <a href="' . Request::getPrevUrl() . '">back.</a>');
        }
    }

}

class Route {

    private $path;
    public $token;
    private $params;
    private $matches = [];
    private $middleware;

    public function __construct($path, $callableOrOptions, $middleware = null, $token = false) {
        $this->path = $path;
        $this->callable = $callableOrOptions;
        $this->middleware = $middleware;
        $this->token = $token;
    }

    public function equals($url) {
        $this->path = trim(preg_replace_callback('/:([\w]+)/', [$this, 'fetchParams'], $this->path), '/');
        if (empty($this->path)) {
            $this->path = '/';
        }
        if (preg_match("#^{$this->path}$#", $url, $this->matches)) {
            array_shift($this->matches);
            return true;
        } else {
            return false;
        }
    }

    public function exec() {
        if (is_callable($this->middleware)) {
            return call_user_func($this->middleware, [$this, 'call']);
        } else if (!empty ($this->middleware) && is_string($this->middleware)) {
            $middleware = "App\\Http\\Middlewares\\{$this->middleware}Middleware";
            $middleware = new $middleware;
            return call_user_func([$middleware, 'control'], [$this, 'call']);
        } else {
            return $this->call();
        }
    }

    public function with($param, $pattern) {
        if (isset($this->params[$param])) {
            $this->params[$param] = $pattern;
        }
    }

    public function call() {
        if (is_callable($this->callable)) {

            return call_user_func_array($this->callable, $this->matches);
        } else if (is_string($this->callable)) {
            $parts = multiexplode(['@', '>', '.', '#'], $this->callable);
            $controller = "App\\Http\\Controllers\\{$parts[0]}Controller";
            $controller = new $controller;

            return call_user_func_array([$controller, $parts[1]], $this->matches);
        }
    }

    private function fetchParams($match) {
        if (isset($this->params[$match[1]])) {
            return $this->params[$match[1]];
        }
        return "([^/]+)";
    }

}
