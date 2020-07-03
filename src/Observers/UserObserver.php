<?php

namespace Mchuluq\Laravel\Uac\Observers;

use Mchuluq\Laravel\Uac\Models\User;
use Illuminate\Support\Facades\Cache;

use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;

use Webpatser\Uuid\Uuid;

class UserObserver {

    use UacHelperTrait;

    protected $cache_driver = 'file';
    protected $cache_ttl = 120;
    
    function __construct(){
        $this->cache_driver = config('uac.cache_driver');
        $this->cache_ttl = config('uac.cache_ttl');
    }

    public function creating(User $user){
        $user->user_id = Uuid::generate();
    }

    public function saving(User $user){
        unset($user->roles,$user->permissions,$user->access_data);
    }    
    public function saved(User $user){
        $user = $user->getRoles()->getPermissions()->getAccessData();
        Cache::store($this->cache_driver)->put("user.{$user->user_id}", $user,$this->cache_ttl);
    }

    public function deleted(User $user){
        Cache::store($this->cache_driver)->forget("user.{$user->user_id}");
    }
    
    public function restored(User $user){
        $user = $user->getRoles()->getPermissions()->getAccessData();
        Cache::store($this->cache_driver)->put("user.{$user->user_id}", $user,$this->cache_ttl);
    }

    public function retrieved(User $user){
        $user = $user->getRoles()->getPermissions()->getAccessData();
        Cache::store($this->cache_driver)->add("user.{$user->user_id}", $user, $this->cache_ttl);
    }
}