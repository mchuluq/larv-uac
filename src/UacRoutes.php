<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'web'], function () {
    // AUTH
    Route::match(['get', 'post'],'/auth/login', 'Mchuluq\Laravel\Uac\Auth\AccountController@doLogin')->name('auth.login');
    Route::match(['get', 'post'],'/auth/logout', 'Mchuluq\Laravel\Uac\Auth\AccountController@doLogout')->name('auth.logout');
    Route::match(['get', 'post'],'/password/forgot', 'Mchuluq\Laravel\Uac\Auth\AccountController@passwordForgot')->name('password.forgot');
    Route::match(['get'],'/password/reset/{token}', 'Mchuluq\Laravel\Uac\Auth\AccountController@passwordReset')->name('password.reset');
    Route::match(['post'],'/password/reset', 'Mchuluq\Laravel\Uac\Auth\AccountController@passwordReset')->name('password.update');
    Route::match(['get','post'],'password/confirm', 'Mchuluq\Laravel\Uac\Auth\AccountController@passwordConfirm')->name('password.confirm');
});