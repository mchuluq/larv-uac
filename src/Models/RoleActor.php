<?php

namespace Mchuluq\Laravel\Uac\Models;

use Mchuluq\Laravel\Uac\Models\BaseModel;
use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as helper;

use Carbon\Carbon;

class RoleActor extends BaseModel{

    use helper;

    protected $table = 'role_actors';

    protected $fillable = array(
        'id',
        'role_name',
        'user_id',
        'group_name'
    );

    public $timestamps = false;

    function assign($for,$role,$type='user_id'){
        $data = array();
        if(in_array($type,['user_id','group_name'])){
            if(is_array($role)){
                foreach ($role as $key=>$r){
                    $data[$key]['role_name'] = $r;
                    $data[$key][$type] = $for;
                    $data[$key]['created_at'] = Carbon::now();
                }
            }else{
                $data[0]['role_name'] = $role;
                $data[0][$type] = $for;
                $data[0]['created_at'] = Carbon::now();
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