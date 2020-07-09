<?php

return array(
    'unauthenticated_redirect_uri' => '/auth/login',
    
    'password_algorithm' => PASSWORD_BCRYPT,
    'password_options' => [
        'cost' => 5
    ],

    'gravatar_options' => [
        's' => 80,
        'd' => 'retro',
        'r' => 'g',
    ],

    'user_type_list' => [
        'internal' => 'Internal',
        'external' => 'External'
    ],

    'access_data_list' => [
        'data-type-1' => array(
            'a1' => 'A-1',
            'a2' => 'A-2',
            'a3' => 'A-3',
        ),
        'data-type-2' => array(
            'b1' => 'B-1',
            'b2' => 'B-2',
        )
    ],
    
    'cache_driver' => 'file',
    'cache_ttl' => 604800,
    'object_storage_driver' => 'file',

    'views' => [
        'login' => 'auth.login',
        'email' => 'auth.passwords.email',
        'reset' => 'auth.passwords.reset',
        'confirm' => 'auth.passwords.confirm'
    ],

    'route' => true,

    'login_max_attempts' => 3, //times
    'login_decay' => 30, // minutes
);