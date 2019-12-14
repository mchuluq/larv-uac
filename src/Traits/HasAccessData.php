<?php

namespace Mchuluq\Laravel\Uac\Traits;

use Mchuluq\Laravel\Uac\Models\Permission;

use Mchuluq\Laravel\Uac\Models\User;
use Mchuluq\Laravel\Uac\Models\AccessData;

trait HasAccessData {
    
    function assignAccessData($access,$type){
        $access = new AccessData();
        $this->removeAccessData();
        if($this instanceof User){
            return (!is_null($this->user_id)) ? $access->assign($this->user_id,$access,$type) : false;
        }
        return;
    }
    function removeAccessData(){
        $access = new AccessData();
        if($this instanceof User){
            return (!is_null($this->user_id)) ? $access->remove($this->user_id) : false;
        }
        return;
    }
    function getAccessData(){
        $access = new AccessData();
        if($this instanceof User){
            $this->attributes['access_data'] = (!is_null($this->user_id)) ? $access->getFor($this->user_id) : false;
        }
        return $this;
    }

}