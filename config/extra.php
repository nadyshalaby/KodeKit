<?php

return [
    'cookie' => [
        'remember_me' => 'hash',
        'remember_me_expiry' => '+1 week',
    ],
    'session' => [
        'remember_me' => 'user',
    ],
    'social' => [
        'facebook' => [
            'app_id' => '628866373922560',
            'app_secret' => '5744a682604b22be38db75b5498e51a9',
            'default_graph_version' => 'v2.5',
        ],
        'google' => [
            'client_id' => '455053498627-vh4prtd0ap51thgeonfmg0vle2gfjvu9.apps.googleusercontent.com',
            'client_secret' => 'z2Hrjtv-WRoEBzHlMAuNFUUt',
            'scopes' => 'email'
        ]
    ]
];

