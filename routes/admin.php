<?php

use App\Http\Middleware\chech_Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api_admin\AdminOperationController;


route::get('/p',function(){
    return 'i like this';
})->middleware(['auth','chech_admin']);

Route::post('/login', [AdminOperationController::class, 'login'])->name('login');

route::group(['middleware'=>'auth'], function(){
    Route::delete('/logout', [AdminOperationController::class, 'logout']);
});

