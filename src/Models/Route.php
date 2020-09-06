<?php

namespace Mchuluq\Laravel\Uac\Models;

use Illuminate\Support\Facades\Route as LaravelRoute;
use Illuminate\Support\Arr;

class Route {

    private $attr = array(
        'uri' => null,
        'label' => null,
        'html_attr' => '',
        'icon' => '',
        'group' => 'menu',
        'position' => 'main',
        'visible' => true,
        'quick_access' => true,
        'access' => []
    );

    public function list($visible_only=true){
        $r = LaravelRoute::getRoutes();
        $data = array();
        foreach ($r as $val) {
            if(isset($val->action['uac'])){
                if($val->action['uac']['visible'] || $visible_only==false){
                    $key = $val->uri();
                    $data[$key] = array_merge($this->attr, $val->action['uac']);
                    if(!$data[$key]['uri']){
                        $data[$key]['uri'] = $key;
                    }
                    $data[$key]['name'] = $val->getName();
                }                
            };            
        }
        return $data;
    }

    function getUserMenu(array $routenames = array()){
        $menus = array();
        $list = $this->list();
        $filtered = Arr::where($list,function($val,$key) use ($routenames){
            return (in_array($val['name'],$routenames));
        });
        foreach ($filtered as $q) {
            $menus[$q['position']][$q['group']][] = $q;
        }
        return $menus;
    }

    function getShortcut(array $routenames = array()){
        $list = $this->list();
        $filtered = Arr::where($list, function ($val, $key) use ($routenames) {
            return (in_array($val['name'], $routenames));
        });
        return Arr::where($filtered,function($val) {
            return ($val['quick_access'] == true);
        });
    }
}