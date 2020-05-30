<?php 

return [
        'id' => [
            'label' => 'ID',
        ],
        'uri_access' => [
            'label' => 'Uri Access',
            'rules' => [
                'insert' => 'required|unique:tasks,uri_access|max:64',
                'update' => 'required|unique:tasks,uri_access,:self|max:64',
            ]
        ],
        'label' => [
            'label' => 'Label',
            'rules' => [
                'insert' => 'required|max:64',
                'update' => 'required|max:64',
            ]
        ],
        'html_attr' => [
            'label' => 'HTML Attribute',
            'rules' => 'max:255'
        ],
        'icon' => [
            'label' => 'Icon',
            'rules' => 'max:64'
        ],
        'group' => [
            'label' => 'Group',
            'rules' => 'max:64'
        ],
        'position' => [
            'label' => 'Position',
            'rules' => 'max:64',
            'list_position' => config('uac.task_position_list')
        ],
        'is_visible' => [
            'label' => 'Visibility',
            'rules' => 'size:1',
            'list' => ['0'=>'Hidden','1'=>'Visible']
        ],
        'is_protected' => [
            'label' => 'Is Protected',
            'rules' => 'size:1',
            'list' => ['0'=>'No','1'=>'Yes']
        ],
        'quick_access' => [
            'label' => 'Quick Access',
            'rules' => 'size:1',
            'list' => ['0'=>'No','1'=>'Yes']
        ],
        'user_type' => [
            'label' => 'User Type',
            'rules' => 'size:1'
        ],
        'menu_order' => [
            'label' => 'Menu Order',
            'rules' => 'numeric'
        ],        
        'description' => [
            'label' => 'Description',
            'rules' => 'max:255'
        ]
    ];