<?php

namespace Mchuluq\Laravel\Uac\Models;

use Mchuluq\Laravel\Uac\Models\BaseModel;
use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as helper;
use Mchuluq\Laravel\Uac\Traits\HasRoleActor;
use Mchuluq\Laravel\Uac\Helpers\ObjectStorage;

class Group extends BaseModel{

    use helper;
    use HasRoleActor;
    use ObjectStorage;

    protected $primaryKey = 'name';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = array(
        'name',        
        'label',
        'description'
    );

    public function setNameAttribute($string){
        $this->attributes['name'] = $this->slugify($string);
    }
}