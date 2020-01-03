<?php

return array(
    'unauthenticated_redirect_uri' => '/login',
    
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
        'pegawai' => 'Dosen/Pegawai',
        'mahasiswa' => 'Mahasiswa'
    ],

    'access_data_list' => [],
    
    'cache_driver' => 'file',
    'cache_ttl' => 604800,
    'object_storage_driver' => 'file',
    'oauth2_scopes' => [
        'account' =>  'Username, Email, Avatar, No. HP',
    ],

    'oauth2_default_scope' => ['account']
);