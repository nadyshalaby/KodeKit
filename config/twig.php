<?php

use App\Classes\User;
use App\Libs\Statics\Func;
use App\Libs\Statics\Url;
use App\Models\GoogleModel;
use Carbon\Carbon;
use Facebook\Facebook;

return [
    'config' => [
        'cache' => Url::resource('cache'),
        'debug' => true, //used for development purposes 
        // 'auto_reload' => true, // if it didn't set it will be determined from the value of debug option
    ],
    /**
     *  list the names of the classes that its functions will be statically called in Twig Environments 
     *  list the objects that its functions will be dynamically called in Twig Environments 
     *  the methods will be called its name pre-pended by the name of its class in lowercase
     *  (eg. Session::has($str) will be called session_has($str)) 
     */
    'static_functions' => [
        'Url',
        'Session',
        'Token',
    ],
    'callable_functions' => [
        'social' => function ($c) {
            switch ($c) {
                case 'f':
                    $url = new Facebook;
                    return $url->getLoginUrl();
                case 'g':
                    $client = new Google_Client;
                    $auth = new GoogleModel($client);
                    return $auth->getAuthUrl();
            }
        },
        'is_loggedin' => function () {
            $u = new User;
            return $u->isLoggedIn();
        },
        'time' => function ($time) {
            $t = new Carbon($time);
            return $t->toRfc850String();
        },
        'readable_time' => function ($time) {
            $t = new Carbon($time);
            return $t->diffForHumans();
        },
        'strip' => function ($string) {
            // strip tags to avoid breaking any html
            $string = strip_tags($string);

            if (strlen($string) > 500) {

                // truncate string
                $stringCut = substr($string, 0, 500);

                // make sure it ends in a word so assassinate doesn't become ass...
                $string = substr($stringCut, 0, strrpos($stringCut, ' '));
            }
            return nl2br($string);
        },
    ],
    'filters' => [
        'e' => function ($str) {
            return Func::escape($str);
        },
    ],
];

