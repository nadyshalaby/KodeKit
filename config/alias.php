<?php

use App\Libs\Statics\Session;
use App\Libs\Statics\Token;
use App\Libs\Statics\Url;

/**
 * Make sure that any of classes using aliases didn't previously imported;
 * */
return [
    'Url' => Url::class,
    'Session' => Session::class,
    'Token' => Token::class,
];
