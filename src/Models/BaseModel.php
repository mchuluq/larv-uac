<?php

namespace Mchuluq\Laravel\Uac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator as Validator;
use Illuminate\Support\Arr;

use Mchuluq\Laravel\Uac\Exception\FormValidationException;

class BaseModel extends Model {

    protected $field_config;

    protected $data_to_validate = [];

    function __construct(array $attributes = []){
        parent::__construct($attributes);
    }

    public function fill(array $attributes){
        $this->setDataToValidate($attributes);
        return parent::fill($attributes);
    }

    public function setDataToValidate(array $attributes){
        $this->data_to_validate = $attributes;
        return $this;
    }

    public function validate ($group=null,$fields = null){
        $validator = Validator::make($this->data_to_validate,$this->getValidationRules($this->data_to_validate,$group,$fields));
        $validator->setAttributeNames($this->fieldAttr('label'));
        if ($validator->fails()){
            throw new FormValidationException ('Validation failed',$validator->errors());
        }
        return $this;
    }

    public function getValidationRules(array $formdata,string $group=null, $fields=null){
        $rules = $this->fieldAttr('rules',false);
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
            if(isset($formdata[$k])){
                $v = str_replace(':self', $formdata[$k], $v);
            }else{
                $v = str_replace(':self', 0, $v);
            }
        });
        return $final_rules;
    }

    public function fieldAttr($item,$default=NULL){
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

    public function dropdown(string $id=null,$label=null){
        $id = ($id) ? $id : $this->primaryKey;
        $label = (!$label) ? $id : $label;
        $get = $this->get()->toArray();
        $lists = [];
        foreach($get as $row){
            $val = (is_array($label)) ? implode(' ',array_values(Arr::only($row,$label,null))) : $row[$label];
            $lists[$row[$id]] = $val;
        }
        return $lists;
    }

}