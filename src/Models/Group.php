<?php

namespace Mchuluq\Laravel\Uac\Models;

use Illuminate\Database\Eloquent\Model;
use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as helper;
use Mchuluq\Laravel\Uac\Traits\HasRoleActor;
use Mchuluq\Laravel\Uac\Helpers\ObjectStorage;

class Group extends Model{

    use helper;
    use HasRoleActor;
    use ObjectStorage;

    protected $primaryKey = 'name';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = array(
        'name',        
        'label',
        'description'
    );

    public function setNameAttribute($string){
        $this->attributes['name'] = $this->slugify($string);
    }

}