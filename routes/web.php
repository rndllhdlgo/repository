<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;


// Auth::routes();
Auth::routes(['register' => false, 'verify' => false, 'confirm' => false]);

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


Route::controller(HomeController::class)->group(function(){
    Route::get('/', 'index');
    Route::get('/logs', 'logs');
    Route::get('/index/data', 'index_data');
    Route::get('/index/logs/reload', 'logs_reload');
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
    Route::get('/users/permissions', 'users_permissions');
    Route::get('/users/stores', 'users_stores');
    Route::get('/users/areas', 'users_areas');
    Route::any('/change/validate', 'change_validate');
    Route::any('/change/password', 'change_password');
});