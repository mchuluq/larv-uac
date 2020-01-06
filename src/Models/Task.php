<?php

namespace Mchuluq\Laravel\Uac\Models;

use Mchuluq\Laravel\Uac\Models\BaseModel;
use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as helper;

class Task extends BaseModel{

    use helper;

    protected $fillable = array(
        'uri_access',
        'label',
        'html_attr',
        'icon',
        'group',
        'position',
        'is_visible',
        'is_protected',
        'menu_order',
        'quick_access',
        'quick_access',
        'user_type',
        'description'
    );

    public $timestamps = true;

    function getMenu(array $uri_access = array()){
        $query = $this->where('is_visible','1')->whereIn('uri_access',$uri_access)->orderBy('menu_order,position,group, label','ASC')->groupBy('uri_access')->get()->toArray();
        return $query;
    }

    function getQuickAccess(array $permissions = array()){
        return $this->whereIn('task_access',$permissions)->where('quick_access','1')->order_by('task_order,task_name','ASC')->get()->toArray();
    }
}