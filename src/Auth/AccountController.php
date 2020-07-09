<?php

namespace Mchuluq\Laravel\Uac\Auth;

use Mchuluq\Laravel\Uac\Traits\Account;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller {
    
    use Account;

    function doLogin(Request $req){
        if($this->hasTooManyLoginAttempts($req)){
            if ($req->isJson() || $req->wantsJson() ) {
                return response()->json([
                    'error' => [
                        'status_code' => Response::HTTP_TOO_MANY_REQUESTS,
                        'code'        => 'TOO_MANY_REQUEST',
                        'message' => 'Too many login attempts. Please try again later.'
                    ],
                ], Response::HTTP_TOO_MANY_REQUESTS);
            }else{
                return abort(Response::HTTP_TOO_MANY_REQUESTS, 'Too many login attempts. Please try again later.');
            }
        }else{
            if($req->isMethod('post')){
                return $this->login($req);
            }else{
                $data['title'] = 'Login';
                return view(config('uac.views.login'),$data);
            }
        }
    }

    function doLogout(Request $req){
        return $this->logout($req);
    }
    
    function passwordForgot(Request $req){
        if($req->isMethod('post')){
            return $this->sendResetLinkEmail($req);
        }else{
            $data['title'] = 'Forgot password';
            return view(config('uac.views.email'),$data);
        }
    }
    function passwordReset(Request $req, $token=null){
        if($req->isMethod('post')){
            return $this->reset($req);
        }else{
            $data['title'] = 'Forgot password';
            return view(config('uac.views.reset'),$data)->with(
                ['token' => $token, 'email' => $req->email]
            );
        }
    }
    
    function passwordConfirm(Request $req){
        if($req->isMethod('post')){
            return $this->confirm($req);
        }else{
            return view(config('uac.views.confirm'));
        }
    }
}
