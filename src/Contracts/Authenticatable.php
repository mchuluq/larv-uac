<?php

namespace Mchuluq\Laravel\Uac\Contracts;

use Illuminate\Contracts\Auth\Authenticatable as BaseAuthenticatable;

interface Authenticatable extends BaseAuthenticatable{
    public function logins();
}
