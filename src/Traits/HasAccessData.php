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

    function isHasAccessData($requirement=null,$type=null){
        if(!isset($this->attributes['access_data'][$type])){
            return false;
        }
        $current_access = $this->attributes['access_data'][$type];
        if(!$requirement && $current_access){
            return true;
        }
        if(is_array($requirement)){
            if(!$current_access){
                return false;
            }
            $status = false;
            foreach($requirement as $req){
                if(in_array($req,$current_access)){
                    $status = true;
                };
            } 
            return $status;
        }else{
            if(!$current_access){
                return false;
            }
            return (in_array($requirement,$current_access));
        }
    }

}