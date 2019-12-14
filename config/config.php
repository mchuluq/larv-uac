<?php

return array(
    'unauthenticated_redirect_uri' => '/login',
    
    'password_algorithm' => PASSWORD_BCRYPT,
    'password_options' => [
        'cost' => 5
    ],    
    // BCRYPT :: ['salt'=>'isi salt','cost'=>5], 
    // ARGON :: ['memory_cost'=>PASSWORD_ARGON2_DEFAULT_MEMORY_COST,'time_cost'=>PASSWORD_ARGON2_DEFAULT_TIME_COST,'threads'=>PASSWORD_ARGON2_DEFAULT_THREADS]

    'gravatar_options' => [
        's' => 80,
        'd' => 'retro',
        'r' => 'g',
    ],

    'user_type_list' => [
        'pegawai' => 'Dosen/Pegawai',
        'mahasiswa' => 'Mahasiswa'
    ],

    'access_data_list' => [
        'biro' => array(
            'baak' => 'BAAK',
            'bau' => 'BAU',
            'psdm' => 'PSDM',
            'bikma' => 'BIKMA',
            'lppm' => 'LPPM',
            'bak' => 'BAK',
            'lpm' => 'LPM',
            'psbtp' => 'PSBTP',
            'kerjasama' => 'UPT Kerjasama',
            'pengajaran' => 'UPT Pengajaran',
            'perpus' => 'Perpustakaan',
            'lab' => 'Laboratorium',
            'prodi' => 'Prodi',
            'fakultas' => 'Fakultas',
        ),
        'fakultas' => array(
            'agama islam' => 'Agama Islam',
            'teknik' => 'Teknik',
            'pertanian' => 'Pertanian',
            'sosial politik' => 'Ilmu Sosial & Ilmu Politik',
            'psikologi' => 'Psikologi',
        ),
        'prodi' => array(
            ''
        )
    ],
    
    'ip2location' => array(
        'service' => 'http://api.ipinfodb.com',
        'key' => '32d9497ded53a921a483c3e95db96c7a41a2f49e42f784e40d91a018fe762c03',
        'version' => 'v3',
    ),
    
    'cache_driver' => 'file',
    'cache_ttl' => 604800,

    'object_storage_driver' => 'file',


    'oauth2_scopes' => [
        'akademik' => 'Nomor Induk Pegawai / Mahasiswa, Prodi, Fakultas',
        'account' =>  'Username, Email, Avatar, No. HP',
        'permissions' =>  'Akses data SISTER',
    ],

    'oauth2_default_scope' => ['account']
);