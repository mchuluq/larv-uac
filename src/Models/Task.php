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
        'user_type',
        'description'
    );

    function getMenu(array $uri_access = array()){
        $menus = array();
        $query = $this->select('uri_access','label','html_attr','icon','group','position','description')->where('is_visible','1')->whereIn('uri_access',$uri_access)->orderBy('menu_order,position,group, label','ASC')->groupBy('uri_access')->get()->toArray();
        foreach($query as $q){
            $menus[$q['position']][$q['group']][] = $q;
        }
        return $menus;
    }

    function getQuickAccess(array $uri_access = array()){
        return $this->select('uri_access','label','html_attr','icon','description')->where('is_visible','1')->whereIn('uri_access',$uri_access)->where('quick_access','1')->order_by('menu_order,label','ASC')->get()->toArray();
    }
}