<?php

namespace Mchuluq\Laravel\Uac;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class UacServiceProvider extends ServiceProvider{

    public function register(){
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'uac');
    }

    public function boot(){
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        Auth::extend('uac', function ($app, $name, $config) {
            $provider = $app['auth']->createUserProvider($config['provider'] ?? null);
            $guard = new SessionGuard($name, $provider, $app['session.store'], request(), $config['expire'] ?? null);
            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($app['cookie']);
            }
            if (method_exists($guard, 'setDispatcher')) {
                $guard->setDispatcher($app['events']);
            }
            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
            }
            return $guard;
        });

        Auth::provider('uac-user', function ($app, array $config) {
            return new UacUserProvider($app['hash'], $config['model']);
        });
    }
}
