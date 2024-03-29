<?php

namespace Mchuluq\Laravel\Uac\Observers;

use Mchuluq\Laravel\Uac\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;

class UserObserver {

    use UacHelperTrait;

    protected $cache_driver = 'file';
    protected $cache_ttl = 120;
    
    function __construct(){
        $this->cache_driver = config('uac.cache_driver');
        $this->cache_ttl = config('uac.cache_ttl');
    }

    public function creating(User $user){
        $user->user_id = (string) Str::uuid();
        if($user->api_token == '1' || $user->api_token == 'yes'){
            $user->api_token = Str::random(100);
        }
    }

    public function saving(User $user){
        if($user->api_token == '1' || $user->api_token == 'yes'){
            $user->api_token = Str::random(100);
        } elseif($user->api_token == '0' || $user->api_token == 'no') {
            $user->api_token = null;
        }
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
