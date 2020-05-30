<?php

namespace Mchuluq\Laravel\Uac\Middlewares;

use Illuminate\Support\Facades\Auth;
use Closure;

class ApiPermission {    

    public function handle($request, Closure $next){
        if(!Auth::check()){
            return redirect(config('uac.unauthenticated_redirect_uri'),'/')->with('message','You need to login first');
        }

        $user = Auth::user();
        $route_name = $request->route()->getAction('as');
        $path_uri = $request->path();

        if($user->isHasPermission($path_uri) || $user->isHasPermission($route_name)){
            return $next($request);
        }else{
            return $this->setAbortResponse($request);
        }
    }

    function setAbortResponse($request){
        if ( $request->isJson() || $request->wantsJson() ) {
            return response()->json([
                'error' => [
                    'status_code' => 401,
                    'code'        => 'INSUFFICIENT_PERMISSIONS',
                    'message' => 'You are not authorized to access this resource.'
                ],
            ], 401);
        }else{
            return abort(401, 'You are not authorized to access this resource.');
        }
    }

}