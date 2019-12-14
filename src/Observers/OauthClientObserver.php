<?php

namespace Mchuluq\Laravel\Uac\Observers;

use Mchuluq\Laravel\Uac\Models\Client;
use Illuminate\Support\Facades\Cache;

use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;

class OauthClientObserver {

    use UacHelperTrait;

    public function creating(Client $client){
        $client->id = $this->guid('oauth_client_id');
    }
    
    
}

// client ID = 29497317-dbfb-0013-39f0-7105b03c7f27
// client secret = rJpyjHt7UOVNK2ZmrMlLQhSk50yXesMSrcFb9Syk