<?php

namespace Mchuluq\Laravel\Uac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator as Validator;
use Illuminate\Support\Arr;

use Mchuluq\Laravel\Uac\Exception\ModelValidationException;

class BaseModel extends Model {

    protected $field_config;

    protected $data_to_validate = [];

    protected $uniques = [];

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
            throw new ModelValidationException ('Validation failed',[
                'details' => $validator->errors()->toArray(),
                'values' => $this->data_to_validate
            ]);
        }
        return $this;
    }

    public function getValidationRules(array $formdata,string $group=null, $fields=null){
        $rules = $this->fieldAttr('rules',false);
        $final_rules = [];
        $id = (isset($this->attributes[$this->primaryKey])) ? $this->attributes[$this->primaryKey] : null;
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
        array_walk($final_rules, function (&$v, $k) use($formdata,$id){
            if($id){
                $v = str_replace(':self', $id, $v);
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

    public function isDuplicate(){
        $id = $this->primaryKey;
        if(empty($this->uniques)){
            return $this;
        }
        $uniques = $this->uniques;
        $data = Arr::where($this->attributes, function ($value, $key) use ($uniques) {
            return in_array($key,$uniques);
        });
        if($this->{$id}){
            $count = $this->where($data)->where($this->primaryKey,'!=',$id)->count();
        }else{
            $count = $this->where($data)->count();
        }
        if($count > 0){
            $errors = [];
            foreach($this->uniques as $r){
                $errors[$r] = 'Record with this value is exists';
            }
            // throw new ModelValidationException ('Record exists',$errors);
            throw new ModelValidationException ('Validation failed',[
                'details' => $errors,
                'values' => $data
            ]);
        }
        return $this;
    }
}