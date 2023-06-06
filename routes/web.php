<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;

Route::fallback(function(){return redirect('/');});
Auth::routes(['register' => false, 'verify' => false, 'confirm' => false]);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::middleware(['session'])->group(function () {
    Route::controller(EventController::class)->group(function(){
        Route::post('/save_sales_invoice', 'save_sales_invoice');
    });

    Route::controller(HomeController::class)->group(function(){
        Route::get('/', 'index');
        Route::get('/logs', 'logs');
        Route::get('/index/data', 'index_data');
        Route::get('/index/logs/reload', 'logs_reload');
    });

    Route::controller(PageController::class)->group(function(){
        Route::get('/si', 'si');
        Route::get('/dr', 'dr');
    });

    Route::controller(UserController::class)->group(function(){
        Route::get('/users', 'users');
        Route::get('/users/data', 'users_data');
        Route::get('/users/reload', 'users_reload');
        Route::any('/users/validate/save', 'validate_users_save');
        Route::any('/users/save', 'users_save');
        Route::any('/users/validate/update', 'validate_users_update');
        Route::any('/users/update', 'users_update');
        Route::any('/users/status', 'users_status');
        Route::any('/change/validate', 'change_validate');
        Route::any('/change/password', 'change_password');
    });
});