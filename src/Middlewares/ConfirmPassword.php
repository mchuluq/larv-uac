<?php

namespace Mchuluq\Laravel\Uac\Middlewares;

use Illuminate\Support\Facades\Auth;
use Closure;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;

class ConfirmPassword{
    
    protected $responseFactory;

    protected $urlGenerator;
    protected $passwordTimeout;

    public function __construct(ResponseFactory $responseFactory, UrlGenerator $urlGenerator, $passwordTimeout = null){
        $this->responseFactory = $responseFactory;
        $this->urlGenerator = $urlGenerator;
        $this->passwordTimeout = $passwordTimeout ?: 10800;
    }

    public function handle($request, Closure $next, $redirectToRoute = null){
        if ($this->shouldConfirmPassword($request)) {
            if ($request->expectsJson()) {
                return $this->responseFactory->json([
                    'message' => 'Password confirmation required.',
                ], 423);
            }
            return $this->responseFactory->redirectGuest(
                $this->urlGenerator->route($redirectToRoute ?? 'password.confirm')
            );
        }
        return $next($request);
    }

    protected function shouldConfirmPassword($request){
        $confirmedAt = time() - $request->session()->get('uac.password_confirmed_at', 0);
        return $confirmedAt > $this->passwordTimeout;
    }
}
