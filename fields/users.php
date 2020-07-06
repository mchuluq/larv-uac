<?php

return array(
    'user_id' => [
        'label' => 'user ID',
        'rules' => 'required|max:36|unique:users,id',
    ],
    'username' => [
        'label' => 'username',
        'rules' => [
            'insert' => 'required|max:64|min:4|alpha_dash|unique:users,username',
            'update' => 'required|max:64|min:4|alpha_dash|unique:users,username.:self',
        ],
        'ask' => 'Login Identification (username) ?',
    ],
    'password' => [
        'label' => 'password',
        'rules' => 'max:32|min:6|alpha_dash|different:username',
        'ask' => 'Password'
    ],
    'fullname' => [
        'label' => 'Fullname',
        'rules' => 'required|max:255',
        'ask' => 'Fullname display ?'
    ],    
    'email' => [
        'label' => 'Email',
        'rules' => [
            'insert' => 'required|email:rfc,dns|unique:users,email',
            'update' => 'required|email:rfc,dns|unique:users,email,:self',
        ],
        'ask' => 'Active Email ?',
    ],
    'phone' => [
        'label' => 'Phone Number',
        'rules' => 'max:15',
        'ask' => 'Phone number ?'
    ],
    'avatar_url' => [
        'label' => 'Avatar URL',
    ],
    'is_disabled' => [
        'label' => 'Status',
        'rules' => [
            'insert' => 'required|in:0,1',
            'update' => 'in:0,1',
        ],
        'list' => ['0'=>'Active','1'=>'Suspend']
    ],
    'user_type' => [
        'label' => 'User Type',
        'rules' => 'required',
        'list' => config('uac.user_type_list'),
        'ask' => 'What type of this user ?'
    ],
    'group_name' => [
        'field' => 'group_name',
        'label' => 'Grup',
        'rules' =>  'required',
        'ask' => 'Which group ?',
        'list' =>  \Mchuluq\Laravel\Uac\Models\Group::pluck('name')->toArray()
    ],
    'user_code_number' => [
        'label' => 'Registration Number',
        'rules' => 'required|max:20',
        'ask' => 'Internal Registration Number'
    ],   
    'settings' => [
        'label' => 'Settings',
    ],
    'api_token' => [
        'field' => 'api_token',
        'label' => 'Api Token',
        'ask' => 'Use API Token ?',
        'list' => ['0'=>'no','1'=>'yes']
    ],

    'password_retype' => [
        'label' => 'Retype password',
        'rules' => 'required|same:password',
        'ask' => 'Type password again',
    ]
);
