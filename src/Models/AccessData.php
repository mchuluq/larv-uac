<?php

namespace Mchuluq\Laravel\Uac\Models;

use Mchuluq\Laravel\Uac\Models\BaseModel;
use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as helper;
use Carbon\Carbon;

class AccessData extends BaseModel{

    use helper;

    protected $table = 'access_data';

    protected $fillable = array(
        'user_id',
        'access_name',
        'access_type'        
    );

     function assign($user_id,$access_name,$access_type){
        $data = array();
        if(in_array($access_type,$this->getAccessType)){
            if(is_array($access_name)){
                foreach ($access_name as $key=>$acc){
                    $data[$key]['access_name'] = $acc;
                    $data[$key]['access_type'] = $access_type;
                    $data[$key]['user_id'] = $user_id;
                    $data[$key]['created_at'] = Carbon::now();
                }
            }else{
                $data[0]['access_name'] = $access_name;
                $data[0]['access_type'] = $access_type;
                $data[0]['user_id'] = $user_id;
                $data[0]['created_at'] = Carbon::now();
            }
        }
        $this->insert($data);
    }

    function remove($user_id){
        if(in_array($type,$this->getAccessType)){
            $this->where(['user_id'=>$user_id])->delete();
        }
    }

    function getFor($user_id,$access_type=NULL){
        $result = [];
        if($access_type){
            $this->where('access_type',$access_type);
        }
        $get = $this->where(['user_id'=>$user_id])->get();
        foreach($get as $pr){
            if($access_type){
                $result[] = $pr->access_name;
            }else{
                $result[$pr->access_type][] = $pr->access_name;
            }
        }
        return $result;
    }

    function getAccessType(){
        $get = config('uac.access_data_list',array());
        return array_keys($get);
    }
}