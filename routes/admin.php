<?php

use App\Http\Middleware\chech_Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api_admin\AdminOperationController;
//Route::prefix('api')->group(function () {

// route::get('/p',function(){
//     return 'i like this';
// })->middleware(['auth','chech_admin']);

// Route::post('/login', [AdminOperationController::class, 'login'])->name('login');

// route::group(['middleware'=>'auth'], function(){
//     Route::post('/logout', [AdminOperationController::class, 'logout']);
// });

// route::get('/m', function(){
//     return 'ooook';
// });

//});
