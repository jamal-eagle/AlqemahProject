<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api_out_user\DisplayController;
use App\Http\Controllers\Api_out_user\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api_student\Student_operationController;
use App\Http\Controllers\Api_admin\AdminOperationController;


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


route::get('/pp',function(){
    dd('iam here');
})->middleware('ckeck_admin');

/*******************************************************out user*******************************************************/
Route::prefix('out_user')->group(function () {
    //تسجيل دخول
    Route::post('/login', [AdminOperationController::class, 'login']);
    //تسجيل طلب للتسجيل بالمعهد
    Route::post('/add-order',[OrderController::class,'CreateOrderForJoinToSchool']);
    //تسجيل طلب للتسجيل بدورة معينة
    Route::post('/add-order-course/{course_id}',[OrderController::class,'CreateOrderForCourse']);
    //عرض كل مدرسي المعهد
    Route::get('/all-teatcher',[DisplayController::class,'all_teatcher']);
    //عرض معلومات مدرس معين
    Route::get('/info-teatcher/{teatcher_id}',[DisplayController::class,'info_teatcher']);
    //عرض جميع الدورات الموجودة بالمعهد
    Route::get('/all_course',[DisplayController::class,'all_course']);
    //عرض معلومات دورة معينة
    Route::get('/info_course/{id_course}',[DisplayController::class,'info_course']);

    /////لا تنسى عرض الاعلانات
});

/*******************************************************all user*******************************************************/
Route::group(['middleware'=>'auth:sanctum'], function(){
    Route::post('/logout', [AdminOperationController::class, 'logout']);
        ///عرض البروفايل
        Route::post('/show_profile',[AuthController::class,'get_profile']);
        ////تعديل البروفايل
        Route::post('/edit_profile{id}',[AuthController::class,'update_profile']);
});



/*******************************************************admin*******************************************************/
Route::prefix('admin')->middleware(['auth:sanctum','check_a'])->group(function () {
    ///خلق حسابات الطلاب
    Route::post('/register_student/{order_id}',[AdminOperationController::class,'register_student']);
    ///خلق حساب للأهل
    Route::post('/register_parentt',[AdminOperationController::class,'register_parentt']);
    //عرض طلبات التسجيل بالمعهد
    Route::get('/display_order',[AdminOperationController::class,'DisplayOrderNewStudent']);
    ///عرض البروفايل
    Route::post('/show_profile',[AuthController::class,'get_profile']);
    ////تعديل البروفايل
    Route::post('/edit_profile{id}',[AuthController::class,'update_profile']);
    ////عرض الطلاب من خلال الاختصاص
    Route::post('/disply_student_classification/{classifaction}',[AdminOperationController::class,'student_classification']);
    //عرض طلاب السنة الواحدة
    Route::post('/disply_all_student_here/{year}',[AdminOperationController::class,'disply_all_student_here']);
    //اطاء الموعد للطلاب
    route::post('/give_date/{order_id}',[AdminOperationController::class,'GiveDate']);
});

/*******************************************************student*******************************************************/
Route::prefix('student')->middleware(['auth:sanctum'])->group(function () {
    //عرض مواد الطالب
    Route::get('/my_subject',[Student_operationController::class,'display_subject']);
    //عرض الملفات و الصور للمادة المختارة
    Route::get('/file_subject/{subject_id}',[Student_operationController::class,'display_file_subject']);
    //الكورسات يلي مسجل فيها الطالب
    Route::get('/my_course',[Student_operationController::class,'my_course']);
});














