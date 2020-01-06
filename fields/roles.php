<?php

return [
    'name' => [
       'label' => 'Name',
       'rules' => [
       'insert' => 'required|unique:roles,name|max:64',
           'update' => 'required|unique:roles,name,:self|max:64',
        ]
    ],
    'label' => [
       'label' => 'Label',
       'rules' => [
           'insert' => 'required|max:64',
           'update' => 'required|max:64',
        ]
    ],
    'description' => [
       'label' => 'Description',
       'rules' => 'max:255'
    ]
];