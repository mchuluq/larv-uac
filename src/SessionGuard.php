<?php

namespace Mchuluq\Laravel\Uac;

use Illuminate\Support\Str;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Auth\UserProvider;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Auth\SessionGuard as BaseGuard;
use Illuminate\Auth\Events\Logout as LogoutEvent;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

use Mchuluq\Laravel\Uac\Helpers\UacHelperTrait as uacHelper;

class SessionGuard extends BaseGuard{

    use uacHelper;

    protected $expire;

    protected $login_id;

    public function __construct($name,UserProvider $provider,Session $session,Request $request = null,$expire = 10080){
        parent::__construct($name, $provider, $session, $request);
        $this->expire = $expire ?: 10080;
    }


    public function user(){
        if ($this->loggedOut) {
            return;
        }
        if (! is_null($this->user)) {
            return $this->user;
        }
        $id = $this->session->get($this->getName());
        if (! is_null($id)) {
            if ($this->user = $this->provider->retrieveById($id)) {
                $this->fireAuthenticatedEvent($this->user);
            }
        }
        $recaller = $this->recaller();
        if (is_null($this->user) && ! is_null($recaller)) {
            $this->user = $this->userFromRecaller($recaller);
            if ($this->user) {
                $this->replaceRememberToken($this->user, $recaller->token());
                $token = $this->retrieveSelectorValidatorCouple($recaller->token());
                $this->customUpdateSession($this->user->getAuthIdentifier(),$token['selector']);
                $this->fireLoginEvent($this->user, true);
            }
        }
        return $this->user;
    }

    protected function replaceRememberToken(AuthenticatableContract $user, $token){
        $oldToken = $this->retrieveSelectorValidatorCouple($token);
        $guid = $oldToken['id'];
        $newToken = $this->generateSelectorValidatorCouple($guid);
        $this->provider->replaceRememberToken($user->getAuthIdentifier(), $token, $newToken, $this->expire);
        $this->queueRecallerCookie($user, $newToken);
    }

    public function login(AuthenticatableContract $user, $remember = false){
        if ($remember) {
            $token = $this->createRememberToken($user);
            $this->queueRecallerCookie($user, $token);
        }else{
            $token = $this->createLogin($user);
        }
        $user->login_id = $token['selector'];
        $this->customUpdateSession($user->getAuthIdentifier(),$token['selector']);
        $this->fireLoginEvent($user, $remember);
        $this->setUser($user);
    }

    function customUpdateSession($user,$login_id){
        parent::updateSession($user);
        $this->session->put('login_id',$login_id);

    }
        

    protected function createRememberToken(AuthenticatableContract $user){
        $guid = $this->guid('login_id');
        $token = $this->generateSelectorValidatorCouple($guid);
        
        $this->provider->purgeRememberTokens($user->getAuthIdentifier(), true);
        $this->provider->addRememberToken($user->getAuthIdentifier(), $token, $this->expire);
        return $token;
    }
    protected function createLogin(AuthenticatableContract $user){
        $guid = $this->guid('login_id');
        $token = $this->generateSelectorValidatorCouple($guid);

        $this->provider->purgeRememberTokens($user->getAuthIdentifier(), true);
        $this->provider->addRememberToken($user->getAuthIdentifier(), $token, false);
        return $token;
    }

    public function logout(){
        $user = $this->user();
        $this->provider->purgeRememberTokens($user->getAuthIdentifier(),true);
        $this->clearUserDataFromStorage();
        if (isset($this->events)) {
            $this->events->dispatch(new LogoutEvent($this->name, $user));
        }
        $this->user = null;
        $this->loggedOut = true;
    }

    protected function clearUserDataFromStorage(){
        $this->session->remove($this->getName());
        $recaller = $this->recaller();
        if (! is_null($recaller)) {
            $this->getCookieJar()->queue($this->getCookieJar()
                    ->forget($this->getRecallerName()));

            $this->provider->deleteRememberToken($recaller->id(), $recaller->token());
        }
    }

    public function logoutOtherDevices($password, $attribute = 'password'){
        if (! $this->user()) {
            return;
        }
        $this->provider->purgeRememberTokens($this->user()->getAuthIdentifier());
        return parent::logoutOtherDevices($password, $attribute);
    }

    protected function queueRecallerCookie(AuthenticatableContract $user, $token = null){
        if (is_null($token)) {
            $token = $this->createRememberToken($user);
        }
        $this->getCookieJar()->queue($this->createRecaller(
            $user->getAuthIdentifier().'|'.$token['user_code'].'|'.$user->getAuthPassword()
        ));
    }

    protected function createRecaller($value){
        return $this->getCookieJar()->make($this->getRecallerName(), $value, $this->expire);
    }
}