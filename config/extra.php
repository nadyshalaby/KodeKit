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
            'app_id' => 'xxxxxxxxxxxxxxxxxxxxxx',
            'app_secret' => 'xxxxxxxxxxxxxxxxxxxxxx',
            'default_graph_version' => 'v2.5',
        ],
        'google' => [
            'client_id' => 'xxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com',
            'client_secret' => 'xxxxxxxxxxxxxxxxxxxxxx',
            'scopes' => 'email'
        ]
    ]
];

