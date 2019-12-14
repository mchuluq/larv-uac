<?php

namespace Mchuluq\Laravel\Uac\Models;

use Laravel\Passport\Client as OauthClient;

use Mchuluq\Laravel\Uac\Observers\OauthClientObserver;

class Client extends OauthClient {

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot(){
        parent::boot();
        static::observe(OauthClientObserver::class);
    }

    protected $casts = [
        'id' => 'string',
        'grant_types' => 'array',
        'personal_access_client' => 'bool',
        'password_client' => 'bool',
        'revoked' => 'bool',
    ];

    public function skipsAuthorization(){
        return $this->firstParty();
    }

}