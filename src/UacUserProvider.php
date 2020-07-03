<?php

namespace Mchuluq\Laravel\Uac;

use Carbon\Carbon;
use Illuminate\Auth\EloquentUserProvider as BaseUserProvider;
use Mchuluq\Laravel\Uac\Contracts\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableUserContract;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use Request;
use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as uacHelper;
use Mchuluq\Laravel\Uac\Models\Task;
use Mchuluq\Laravel\Uac\Contracts\UserProvider;

class UacUserProvider extends BaseUserProvider implements UserProvider{

    use uacHelper;

    protected function getModelByIdentifier($identifier){
        return Cache::store(config('uac.cache_driver'))->remember("user.$identifier", config('uac.cache_ttl'), function() use($identifier) {
            $model = $this->createModel();
            return $this->newModelQuery($model)->where($model->getAuthIdentifierName(), $identifier)->where('is_disabled','0')->first()->getPermissions()->getRoles()->getAccessData();
        });
    }

    public function retrieveByCredentials(array $credentials){
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            array_key_exists('password', $credentials))) {
            return;
        }
        $query = $this->newModelQuery();
        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'password')) {
                continue;
            }
            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }
        return $query->where('is_disabled','0')->first()->getPermissions()->getRoles()->getAccessData();
    }

    public function retrieveById($identifier){
        return Cache::store(config('uac.cache_driver'))->remember("user.$identifier", config('uac.cache_ttl'), function() use($identifier) {
            $model = $this->createModel();
            return $this->newModelQuery($model)->where($model->getAuthIdentifierName(), $identifier)->where('is_disabled','0')->first()->getPermissions()->getRoles()->getAccessData();
        });
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

    public function updateLogout($identifier,$login_id){
        $model = $this->getModelByIdentifier($identifier);
        $token = $model->logins()->where('id', $login_id)->first();
        if ($model && $token) {
            $token->logout = true;
            $token->remember_selector = null;
            $token->remember_validator = null;
            $token->remember_expire = null;
            $token->save();
        }
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

    public function getUserMenu($identifier){
        $task = new Task();
        $model = $this->getModelByIdentifier($identifier);
        return $task->getUserMenu($model->permissions);
    }

    public function getShortcut($identifier){
        $task = new Task();
        $model = $this->getModelByIdentifier($identifier);
        return $task->getShortcut($model->permissions);
    }
    
    public function getPublicMenu(){
        $task = new Task();
        return $task->getPublicMenu();
    }
}
