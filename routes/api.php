<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api_out_user\DisplayController;
use App\Http\Controllers\Api_out_user\OrderController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*-----------------------order-----------------------*/
Route::post('/add-order',[OrderController::class,'CreateOrderForJoinToSchool']);
Route::post('/add-order-course/{course_id}',[OrderController::class,'CreateOrderForCourse']);

/*-----------------------teacher-----------------------*/
Route::get('/all-teatcher',[DisplayController::class,'all_teatcher']);
Route::get('/info-teatcher/{teatcher_id}',[DisplayController::class,'info_teatcher']);

/*-----------------------course-----------------------*/
Route::get('/all_course',[DisplayController::class,'all_course']);
Route::get('/info_course/{id_course}',[DisplayController::class,'info_course']);





//Route::get('/display-order',[OrderController::class,'DisplayOrderNewStudent']);
//route::post('/give_date/{order_id}',[OrderController::class,'GiveDate']);
