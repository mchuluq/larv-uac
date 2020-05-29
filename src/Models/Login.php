<?php

namespace Mchuluq\Laravel\Uac\Models;

use Illuminate\Database\Eloquent\Model;

use Mchuluq\Laravel\Uac\Models\User;

class Login extends Model{
    
    protected $fillable = [
        'id','user_id','login_start','ip_address','user_agent','logout','remember_selector','remember_validator', 'remember_expire',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','user_id');
    }
}
