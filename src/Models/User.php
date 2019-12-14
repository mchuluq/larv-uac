<?php

namespace Mchuluq\Laravel\Uac\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Model;

use Laravel\Passport\HasApiTokens;

use Mchuluq\Laravel\Uac\EloquentAuthenticatable as Authenticatable;

use Mchuluq\Laravel\Uac\Contracts\Authenticatable as AuthenticatableContract;

use Mchuluq\Laravel\Uac\Observers\UserObserver;

use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as uacHelper;
use Mchuluq\Laravel\Uac\Helpers\ObjectStorage;

use Mchuluq\Laravel\Uac\Traits\HasRoleActor;
use Mchuluq\Laravel\Uac\Traits\HasPermission;
use Mchuluq\Laravel\Uac\Traits\HasAccessData;



class User extends Model implements AuthenticatableContract{
    
    use Notifiable;
    use Authenticatable;
    use uacHelper;
    use HasRoleActor;
    use HasAccessData;
    use HasPermission;
    use ObjectStorage;
    use HasApiTokens;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'user_id','username','name', 'email', 'password','fullname','phone','avatar_url','is_disabled','user_type','user_code_number','group_name','settings',
    ];

    protected $hidden = [
        'password',
        'email_verified_at',
        'created_at',
        'updated_at',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_disabled' => 'boolean',
        'settings' => 'array'
    ];

    protected static function boot(){
        parent::boot();
        static::observe(UserObserver::class);
    }

    public function setAvatarUrlAttribute($value){
        if(filter_var($value,FILTER_VALIDATE_EMAIL)){
            $this->attributes['avatar_url'] = $this->getGravatar($this->email);
        }else{
            $this->attributes['avatar_url'] = $value;
        }
    }

    public function setPasswordAttribute($string=null){
        $this->attributes['password'] = password_hash($string,config('uac.password_algorithm'),config('uac.password_options'));
    }

    public function getAuthIdentifierName(){
        return "username";
    }
    
    public function getAuthIdentifier(){
        return $this->{$this->getAuthIdentifierName()};
    }
    
    public function getAuthPassword(){
        return $this->password;
    }

    public function getRememberToken(){
        if (! empty($this->getRememberTokenName())) {
            return $this->{$this->getRememberTokenName()};
        }
    }
    
    public function setRememberToken($value){
        if (! empty($this->getRememberTokenName())) {
            $this->{$this->getRememberTokenName()} = $value;
        }
    }
    
    public function getRememberTokenName(){
        return $this->rememberTokenName;
    }

    public function findForPassport($username){
        return $this->where('username', $username)->where('is_disabled','0')->first();
    }
    public function validateForPassportPasswordGrant($password){
        return password_verify($password, $this->password);
    }
}
