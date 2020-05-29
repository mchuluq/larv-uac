<?php

namespace Mchuluq\Laravel\Uac;

use Carbon\Carbon;
use Illuminate\Auth\EloquentUserProvider as BaseUserProvider;
use Mchuluq\Laravel\Uac\Contracts\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableUserContract;
use Illuminate\Support\Facades\Cache;

use Request;
use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as uacHelper;

class UacUserProvider extends BaseUserProvider implements UserProvider{

    use uacHelper;

    public function retrieveById($identifier){
        return Cache::store(config('uac.cache_driver'))->remember("user.$identifier", config('uac.cache_ttl'), function() use($identifier) {
            return parent::retrieveById($identifier);
        });
        //return Cache::store(config('uac.cache_driver'))->get("user.$identifier") ?? parent::retrieveById($identifier);
    }

    public function retrieveByToken($identifier, $token){
        if (! $model = $this->getModelByIdentifier($identifier)) {
            return null;
        }
        $token = $this->retrieveSelectorValidatorCouple($token);
        $rememberToken = $model->logins()->where('logins.remember_selector',$token['selector'])->where('remember_expire', '>', Carbon::now())->get()->first();
        if($rememberToken){
            if (password_verify($token['validator'],$rememberToken->remember_validator)) {
                return $model;
            }
        }
    }

    public function addRememberToken($identifier, $token, $expire){
        $model = $this->getModelByIdentifier($identifier);
        $data = [
            'id' => $token['id'],
            'user_id' => $identifier,
            'login_start' => Carbon::now()->timestamp,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('user-agent'),
            'logout' => FALSE,
            'remember_selector' => $token['selector'],
            'remember_validator' => $token['validator_hashed'],
            'remember_expire' => ($expire > 0) ? Carbon::now()->addMinutes($expire) : null
        ];
        if ($model) {
            $model->logins()->create($data);
        }
    }

    public function replaceRememberToken($identifier, $token, $newToken, $expire){
        $model = $this->getModelByIdentifier($identifier);
        $token = $this->retrieveSelectorValidatorCouple($token);
        if ($model) {
            $model->logins()->where('id', $token['id'])->update([
                'remember_selector' => $newToken['selector'],
                'remember_validator' => $newToken['validator_hashed'],
                'remember_expire' => Carbon::now()->addMinutes($expire),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    public function deleteRememberToken($identifier, $token){
        $model = $this->getModelByIdentifier($identifier);
        $token = $this->retrieveSelectorValidatorCouple($token);
        if ($model && $token = $model->logins()->where('id', $token['id'])->first()) {
            $token->logout = true;
            $token->remember_selector = null;
            $token->remember_validator = null;
            $token->remember_expire = null;
            $token->save();
        }
    }

    public function purgeRememberTokens($identifier, $expired = false){
        $model = $this->getModelByIdentifier($identifier);
        if ($model) {
            $query = $model->logins();
            if ($expired) {
                $query->where('remember_expire', '<', Carbon::now());
            }
            $query->update([
                'logout' => true,
                'remember_selector' => null,
                'remember_validator' => null,
                'remember_expire' => null,
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    protected function getModelByIdentifier($identifier){
        $model = $this->createModel();
        return $model->where($model->getAuthIdentifierName(), $identifier)->where('is_disabled','0')->first();
    }

    public function validateCredentials(AuthenticatableUserContract $user, array $credentials){
        $plain = $credentials['password']; // will depend on the name of the input on the login form
        $hashedValue = $user->getAuthPassword();
        $verify = password_verify($plain,$hashedValue);
        if($verify && password_needs_rehash($hashedValue,config('uac.password_algorithm'),config('uac.password_options'))){
            $hash = password_hash($plain,config('uac.password_algorithm'),config('uac.password_options'));
            $user->where('user_id', $user->user_id)->update(['password' => $hash]);
        }
        return $verify;
    }
}
