<?php

namespace Mchuluq\Laravel\Uac\Models;

use Mchuluq\Laravel\Uac\Models\BaseModel;
use Mchuluq\Laravel\Uac\Traits\HasPermission;
use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as helper;

class Role extends BaseModel{

    use helper;
    use HasPermission;

    protected $primaryKey = 'name';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = array(
        'name',
        'label',
        'description',
    );

    public function setNameAttribute($string){
        $this->attributes['name'] = $this->slugify($string);
    }

}