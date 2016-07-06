<?php

return [
    'url' => [
        'app' => 'http://localhost/clinic/',
        'error' => [
            '301' => 'error/301.php',
            '401' => 'error/401.php',
            '403' => 'error/403.php',
            '404' => 'error/404.html',
            '500' => 'error/500.php',
        ]
    ],
    'mysql' => [
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'db' => 'clinic',
        'dbrestore' => false, // flag to determine whether to restore the database or not
        'dbfile' => 'clinic.sql', // filename to be used for auto-importing for the database
        'fetch_mode' => PDO::FETCH_OBJ,
    ],
    // is a global middleware for every request will be made 
    // (eg. 'app_middleware' => 'App')
    // (eg. 'app_middleware' => function ($next) { ... return $next(); } )   
    'app_middleware' => '' 
];
