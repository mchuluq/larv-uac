<?php

namespace Mchuluq\Laravel\Uac\Observers;

use Mchuluq\Laravel\Uac\Models\RoleActor;
use Mchuluq\Laravel\Uac\Models\User;

use Illuminate\Support\Facades\Cache;

use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;

class RoleActorObserver {

    use UacHelperTrait;

    protected $cache_driver = 'file';
    protected $cache_ttl = 120;
    
    function __construct(){
        $this->cache_driver = config('uac.cache_driver');
        $this->cache_ttl = config('uac.cache_ttl');
    }

    public function saved(RoleActor $ra){
        // menghapus cache user ketika assign new permission
        if($ra->user_id != null){
            Cache::store($this->cache_driver)->forget("user.{$ra->user_id}");
        }
    }

    public function deleted(RoleActor $ra){
        // menghapus cache user ketika delete permission
        if($ra->user_id != null){
            Cache::store($this->cache_driver)->forget("user.{$ra->user_id}");
        }
    }
}