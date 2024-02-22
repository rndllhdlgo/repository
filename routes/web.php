<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BackUpController;

Route::fallback(function(){return redirect('/');});
Auth::routes(['register' => false, 'verify' => false, 'confirm' => false]);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::controller(QueryController::class)->group(function(){
    Route::get('/check_dr', 'check_dr');
});
Route::middleware(['session'])->group(function () {
    Route::controller(EventController::class)->group(function(){
        Route::post('/save_si', 'save_si');
        Route::post('/save_cr', 'save_cr');
        Route::post('/save_bs', 'save_bs');
        Route::post('/save_or', 'save_or');
        Route::post('/save_dr', 'save_dr');
        Route::post('/edit', 'edit');
        Route::post('/approve', 'approve');
        Route::post('/disapprove', 'disapprove');
        Route::any('/requestEdit', 'requestEdit');
        Route::post('/return_to_encoder', 'return_to_encoder');
        Route::post('/edit_si', 'edit_si');
        Route::post('/edit_cr', 'edit_cr');
        Route::post('/edit_bs', 'edit_bs');
        Route::post('/edit_or', 'edit_or');
        Route::post('/edit_dr', 'edit_dr');
        Route::any('/notif_update', 'notif_update')->name('notif_update');
        Route::any('/checkNext', 'checkNext');
    });

    Route::controller(HomeController::class)->group(function(){
        Route::get('/', 'index');
        Route::get('/logs', 'logs');
        Route::get('/index/data', 'index_data');
        Route::get('/checkURL', 'checkURL');
    });

    Route::controller(PageController::class)->group(function(){
        Route::get('/si', 'si');
        Route::get('/cr', 'cr');
        Route::get('/bs', 'bs');
        Route::get('/or', 'or');
        Route::get('/dr', 'dr');
    });

    Route::controller(TableController::class)->group(function(){
        Route::get('/si_data', 'si_data');
        Route::get('/cr_data', 'cr_data');
        Route::get('/bs_data', 'bs_data');
        Route::get('/or_data', 'or_data');
        Route::get('/dr_data', 'dr_data');
    });

    Route::controller(UserController::class)->group(function(){
        Route::get('/users', 'users');
        Route::get('/users/data', 'users_data');
        Route::any('/users/validate/save', 'validate_users_save');
        Route::any('/users/save', 'users_save');
        Route::any('/users/validate/update', 'validate_users_update');
        Route::any('/users/update', 'users_update');
        Route::any('/users/status', 'users_status');
        Route::any('/users/email', 'users_email');
        Route::any('/change/validate', 'change_validate');
        Route::any('/change/password', 'change_password');
    });
});

Route::controller(BackUpController::class)->group(function(){
    Route::any('/backup', 'backup');
});