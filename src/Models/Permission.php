<?php

namespace Mchuluq\Laravel\Uac\Models;

use Mchuluq\Laravel\Uac\Models\BaseModel;
use Illuminate\Support\Facades\DB;

use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as helper;

use Mchuluq\Laravel\Uac\Models\User;
use Mchuluq\Laravel\Uac\Models\RoleActor;

use Mchuluq\Laravel\Uac\Observers\PermissionObserver;

use Carbon\Carbon;

class Permission extends BaseModel{

    use helper;

    protected $fillable = array(
        'uri_access',
        'user_id',
        'role_name',        
        'group_name'
    );

    public $timestamps = false;

    protected static function boot(){
        parent::boot();
        static::observe(PermissionObserver::class);
    }

    function assign($for,$uri_access,$type='user_id'){
        $data = array();
        if(in_array($type,['user_id','group_name','role_name'])){
            if(is_array($uri_access)){
                foreach ($uri_access as $key=>$uri){
                    $data[$key]['uri_access'] = $uri;
                    $data[$key][$type] = $for;
                    $data[$key]['created_at'] = Carbon::now();
                }
            }else{
                $data[0]['uri_access'] = $uri_access;
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
        foreach($get as $perm){
            $result[] = $perm->uri_access;
        }
        return $result;
    }

    function getPermissions($user_id,$group_name){
        $tperm = $this->getTable();
        $troleact = with(new RoleActor)->getTable();

        $result = [];
        $res = DB::table($tperm." AS a")->select("a.uri_access AS uri_access")
        ->where("a.user_id",$user_id)
        ->orWhere("a.group_name",$group_name)
        ->orWhereRaw("(a.role_name IN (SELECT c.role_name FROM ".$troleact." c WHERE (c.user_id = ?)))",[$user_id])
        ->orWhereRaw("(a.role_name IN (SELECT c.role_name FROM ".$troleact." c WHERE (c.group_name = ?)))",[$group_name])
        ->groupBy('a.uri_access')->get()->toArray();
        foreach($res as $r){
            $result[] = $r->uri_access;
        }
        return $result;
    }
}