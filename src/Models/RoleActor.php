<?php

namespace Mchuluq\Laravel\Uac\Models;

use Mchuluq\Laravel\Uac\Models\BaseModel;
use Mchuluq\Laravel\Uac\Observers\RoleActorObserver;
use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as helper;

class RoleActor extends BaseModel{

    use helper;

    protected $table = 'role_actors';

    protected $fillable = array(
        'id',
        'role_name',
        'user_id',
        'group_name',
        'created_at' 
    );

    public $timestamps = false;

    protected static function boot(){
        parent::boot();
        static::observe(RoleActorObserver::class);
    }

    function assign($for,$role,$type='user_id'){
        $data = array();
        if (!$role) {
            return;
        }
        if(in_array($type,['user_id','group_name'])){
            if(is_array($role)){
                foreach ($role as $key=>$r){
                    $data[$key]['role_name'] = $r;
                    $data[$key][$type] = $for;
                }
            }else{
                $data[0]['role_name'] = $role;
                $data[0][$type] = $for;
            }
        }
        return $this->insert($data);
    }

    function remove($for,$type='user_id'){
        if(in_array($type,['user_id','group_name'])){
            $this->where([$type=>$for])->delete();
        }
    }

    function getFor($for,$type){
        $result = [];
        $get = $this->where([$type=>$for])->get();
        foreach($get as $role){
            $result[] = $role->role_name;
        }
        return $result;
    }
}