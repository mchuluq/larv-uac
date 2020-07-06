<?php

namespace Mchuluq\Laravel\Uac\Observers;

use Mchuluq\Laravel\Uac\Models\Permission;
use Mchuluq\Laravel\Uac\Models\User;

use Illuminate\Support\Facades\Cache;

use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;

class PermissionObserver {

    use UacHelperTrait;

    protected $cache_driver = 'file';
    protected $cache_ttl = 120;
    
    function __construct(){
        $this->cache_driver = config('uac.cache_driver');
        $this->cache_ttl = config('uac.cache_ttl');
    }

    public function saved(Permission $perm){
        // menghapus cache user ketika assign new permission
        if($perm->user_id != null){
            Cache::store($this->cache_driver)->forget("user.{$perm->user_id}");
        }
    }

    public function deleted(Permission $perm){
        // menghapus cache user ketika delete permission
        if($perm->user_id != null){
            Cache::store($this->cache_driver)->forget("user.{$perm->user_id}");
        }
    }
}