<?php

namespace Mchuluq\Laravel\Uac\Models;

use Mchuluq\Laravel\Uac\Models\BaseModel;
use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as helper;

class Role extends BaseModel{

    use helper;

    protected $primaryKey = 'name';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = array(
        'name',
        'label',
        'description',
    );

    protected $field_config;

    public function setNameAttribute($string){
        $this->attributes['name'] = $this->slugify($string);
    }

    public function createRoleCommand(array $details) : self{
        $role = new self($details);
        $role->save();
        return $role;
    }

}