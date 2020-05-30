<?php

namespace Mchuluq\Laravel\Uac\Traits;


use Mchuluq\Laravel\Uac\Models\RoleActor;

use Mchuluq\Laravel\Uac\Models\User;
use Mchuluq\Laravel\Uac\Models\Group;

trait HasRoleActor {
    
    function roles(){
        if($this instanceof User){
            return $this->hasMany(RoleActor::class,'user_id','user_id');
        }elseif($this instanceof Group){
            return $this->hasMany(RoleActor::class,'group_name','name');
        }
        return;     
    }

    function assignRoles($role){
        $role_actors = new RoleActor();
        $this->removeRoles();
        if($this instanceof User){
            return (!is_null($this->user_id)) ? $role_actors->assign($this->user_id,$role,'user_id') : false;
        }elseif($this instanceof Group){
            return (!is_null($this->name)) ? $role_actors->assign($this->name,$role,'group_name') : false;
        }
        return;
    }
    function removeRoles(){
        $role_actors = new RoleActor();
        if($this instanceof User){
            return (!is_null($this->user_id)) ? $role_actors->remove($this->user_id,'user_id') : false;
        }elseif($this instanceof Group){
            return (!is_null($this->name)) ? $role_actors->remove($this->name,'group_name') : false;
        }
        return;
    }

    function getRoles(){
        $role_actors = new RoleActor();
        if($this instanceof User){
            $this->attributes['roles'] = (!is_null($this->user_id)) ? $role_actors->getFor($this->user_id,'user_id') : false;
        }elseif($this instanceof Group){
            $this->attributes['roles'] = (!is_null($this->name)) ? $role_actors->getFor($this->name,'group_name') : false;
        }
        return $this;
    }

    function isHasRole($role){
        if(!isset($this->attributes['roles'])){
            return false;
        }
        return in_array($role,$this->attributes['roles']);
    }

}