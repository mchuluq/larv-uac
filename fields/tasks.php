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
            ],
            'ask' => 'URI Access'
        ],
        'label' => [
            'label' => 'Label',
            'rules' => [
                'insert' => 'required|max:64',
                'update' => 'required|max:64',
            ],
            'ask' => 'Label'
        ],
        'html_attr' => [
            'label' => 'HTML Attribute',
            'rules' => 'max:255',
            'ask' => 'HTML Attribute'
        ],
        'icon' => [
            'label' => 'Icon',
            'rules' => 'max:64',
            'ask' => 'Icon'
        ],
        'group' => [
            'label' => 'Group',
            'rules' => 'required|max:64',
            'ask' => 'Group'
        ],
        'position' => [
            'label' => 'Position',
            'rules' => 'required|max:64',
            'list' => array(
                'main' => 'Utama',
                'top' => 'Atas',
            ),
            'ask' => 'Position'
        ],
        'is_visible' => [
            'label' => 'Visibility',
            'rules' => 'required|size:1',
            'list' => ['0'=>'Hidden','1'=>'Visible'],
            'ask' => 'Visibility'
        ],
        'is_protected' => [
            'label' => 'Is Protected',
            'rules' => 'required|size:1',
            'list' => ['0'=>'No','1'=>'Yes'],
            'ask' => 'Is Protected ?'
        ],
        'quick_access' => [
            'label' => 'Quick Access',
            'rules' => 'required|size:1',
            'list' => ['0'=>'No','1'=>'Yes'],
            'ask' => 'Quick Access ?'
        ],
        'user_type' => [
            'label' => 'User Type',
            'rules' => 'size:1'
        ],
        'menu_order' => [
            'label' => 'Menu Order',
            'rules' => 'numeric',
            'ask' => 'Menu order ?'
        ],        
        'description' => [
            'label' => 'Description',
            'rules' => 'max:255',
            'ask' => 'Description ?'
        ]
    ];