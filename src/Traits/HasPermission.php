<?php

namespace Mchuluq\Laravel\Uac\Traits;

use Mchuluq\Laravel\Uac\Models\Permission;

use Mchuluq\Laravel\Uac\Models\User;
use Mchuluq\Laravel\Uac\Models\Group;
use Mchuluq\Laravel\Uac\Models\Role;

trait HasPermission {
    
    function permissions(){
        if($this instanceof User){
            return $this->hasMany(Permission::class,'user_id','user_id');
        }elseif($this instanceof Group){
            return $this->hasMany(Permission::class,'group_name','name');
        }elseif($this instanceof Role){
            return $this->hasMany(Permission::class,'role_name','name');
        }
        return;     
    }
    function assignPermissions($uri){
        $perm = new Permission();
        $this->removePermissions();
        if($this instanceof User){
            return (!is_null($this->user_id)) ? $perm->assign($this->user_id,$uri,'user_id') : false;
        }elseif($this instanceof Group){
            return (!is_null($this->name)) ? $perm->assign($this->name,$uri,'group_name') : false;
        }elseif($this instanceof Role){
            return (!is_null($this->name)) ? $perm->assign($this->name,$uri,'role_name') : false;
        }
        return;
    }
    function removePermissions(){
        $perm = new Permission();
        if($this instanceof User){
            return (!is_null($this->user_id)) ? $perm->remove($this->user_id,'user_id') : false;
        }elseif($this instanceof Group){
            return (!is_null($this->name)) ? $perm->remove($this->name,'group_name') : false;
        }elseif($this instanceof Role){
            return (!is_null($this->name)) ? $perm->remove($this->name,'role_name') : false;
        }
        return;
    }
    function getPermissions(){
        $perm = new Permission();
        if($this instanceof User){
            $this->attributes['permissions'] = (!is_null($this->user_id) && !is_null($this->group_name)) ? $perm->getPermissions($this->user_id,$this->group_name) : false;
        }elseif($this instanceof Group){
            $this->attributes['permissions'] = (!is_null($this->name)) ? $perm->getFor($this->name,'group_name') : false;
        }elseif($this instanceof Role){
            $this->attributes['permissions'] = (!is_null($this->name)) ? $perm->getFor($this->name,'role_name') : false;
        }
        return $this;
    }

    function isHasPermission($uri_access){
        if(!isset($this->attributes['permissions'])){
            return false;
        }
        return in_array($uri_access,$this->attributes['permissions']);   
    }
}