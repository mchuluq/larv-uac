<?php

namespace Mchuluq\Laravel\Uac\Guards;

use Illuminate\Auth\SessionGuard as BaseGuard;
use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as uacHelper;

use Webpatser\Uuid\Uuid;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Auth\TokenGuard as TGuard;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;



class TokenApiGuard extends TGuard{

    use uacHelper;
    use GuardHelpers;

    public function __construct (UserProvider $provider, Request $request, $configuration) {
		$this->provider = $provider;
		$this->request = $request;
		// key to check in request
		$this->inputKey = isset($configuration['input_key']) ? $configuration['input_key'] : 'api_token';
		// key to check in database
		$this->storageKey = isset($configuration['storage_key']) ? $configuration['storage_key'] : 'api_token';
	}

	public function user () {
		if (!is_null($this->user)) {
			return $this->user;
		}
		$user = null;
		// retrieve via token
		$token = $this->getTokenForRequest();
		if (!empty($token)) {
            // the token was found, how you want to pass?
            $credentials = [$this->storageKey => $token];
			$user = $this->provider->retrieveByCredentials($credentials);
		}
		return $this->user = $user;
	}

	public function getTokenForRequest () {
		$token = $this->request->query($this->inputKey);
		if (empty($token)) {
			$token = $this->request->input($this->inputKey);
		}
		if (empty($token)) {
			$token = $this->request->bearerToken();
		}
		return $token;
	}

	public function validate (array $credentials = []) {
		if (empty($credentials[$this->inputKey])) {
			return false;
		}
		$credentials = [ $this->storageKey => $credentials[$this->inputKey] ];
		if ($this->provider->retrieveByCredentials($credentials)) {
			return true;
		}
		return false;
	}
}