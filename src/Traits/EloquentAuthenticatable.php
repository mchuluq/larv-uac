<?php

namespace Mchuluq\Laravel\Uac\Traits;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable as BaseAuthenticatable;

use Mchuluq\Laravel\Uac\Models\Login;

trait EloquentAuthenticatable{
    use BaseAuthenticatable;
    /**
     * Get the "remember me" session tokens for the user.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logins(){
        return $this->hasMany(Login::class,'user_id','user_id');
    }
}
