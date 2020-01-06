<?php

namespace Mchuluq\Laravel\Uac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator as Validator;

use Mchuluq\Laravel\Uac\Exception\FormValidationException;

class BaseModel extends Model {

    protected $field_config;

    public function validate ($group=null,$fields = null){
        $validator = Validator::make($this->attributes,$this->getValidationRules($this->attributes,$group,$fields));
        $validator->setAttributeNames($this->fetchFieldConfig('label'));
        if ($validator->fails()){
            throw new FormValidationException ('Validation failed',$validator->errors());
        }
        return $this;
    }

    public function getValidationRules(array $formdata,string $group=null, $fields=null){
        $rules = $this->fetchFieldConfig('rules',false);
        $final_rules = [];
        if($group){
            foreach($rules as $key=>$rule){
                if($rule){
                    if(is_string($rule)){
                        $final_rules[$key] = $rule;
                    }elseif(array_key_exists($group,$rule)){
                        $final_rules[$key] = $rule[$group];
                    }
                }
            }
        }else{
            $final_rules = $rules;
        }
        if(is_array($fields)){
            $final_rules = array_filter($final_rules,function($k) use($fields){
                return in_array($k,$fields);
            },ARRAY_FILTER_USE_KEY);
        }
        array_walk($final_rules, function ($v, $k) use($formdata){
            $v = str_replace(':self', $formdata[$k], $v);
        });
        return $final_rules;
    }

    public function fetchFieldConfig($item,$default=NULL){
        $this->_setFieldConfig();
        $data = [];
        foreach($this->field_config as $key=>$val){
            if($default == null || $default !== false){
                $data[$key] = (isset($val[$item])) ? $val[$item] : $default;
            }
        }
        return $data;
    }

    private function _setFieldConfig(){
        if(is_null($this->field_config)){
            $table = $this->getTable();
            $file_name = $table.'.php';
            $file_path = app_path('Fields'.DIRECTORY_SEPARATOR.$file_name);
            if(file_exists($file_path)){
                $this->field_config = include $file_path;
            }else{
                throw new \Exception('Field config not found');
            }
        }
    }

}