<?php

namespace Mchuluq\Laravel\Uac\Observers;

use Mchuluq\Laravel\Uac\Models\AccessData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;

class AccessDataObserver {

    use UacHelperTrait;

    protected $cache_driver = 'file';
    protected $cache_ttl = 120;
    
    function __construct(){
        $this->cache_driver = config('uac.cache_driver');
        $this->cache_ttl = config('uac.cache_ttl');
    }

    public function creating(AccessData $ad){
        $ad->created_at = Carbon::now();
    }

    public function saved(AccessData $ad){
        // menghapus cache user ketika assign new permission
        if($ad->user_id != null){
            Cache::store($this->cache_driver)->forget("user.{$ad->user_id}");
        }
    }

    public function deleted(AccessData $ra){
        // menghapus cache user ketika delete permission
        if($ad->user_id != null){
            Cache::store($this->cache_driver)->forget("user.{$ad->user_id}");
        }
    }
}