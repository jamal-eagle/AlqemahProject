<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api_out_user\DisplayController;
use App\Http\Controllers\Api_out_user\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api_student\Student_operationController;
use App\Http\Controllers\Api_admin\AdminOperationController;
use App\Http\Controllers\Api_all_user\AllUserController;
use App\Http\Controllers\Api_student\StudentPostController;
use App\Http\Controllers\Api_student\MarkController;
use App\Http\Controllers\Api_parentt\ParenttController;


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
    //تعديل طلب تسجيل في دورة
    Route::put('/update/{id}',[OrderController::class,'update']);
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
    route::post('/give_date/{order_id}',[AdminOperationController::class,'GiveDate']);
    //إنشاء حساب للطالب
    Route::post('/register/{order_id}', [AdminOperationController::class, 'registerPost']);
});

/*******************************************************student*******************************************************/
Route::prefix('student')->middleware(['auth:sanctum'])->group(function () {
    //عرض مواد الطالب
    Route::get('/my_subject',[Student_operationController::class,'display_subject']);
    //عرض الملفات و الصور للمادة المختارة
    Route::get('/file_subject/{subject_id}',[Student_operationController::class,'display_file_subject']);
    //تسجيل طالب في دورة
    Route::post('/create-order-course/{course_id}',[Student_operationController::class,'orderCourse']);
    //الكورسات يلي مسجل فيها الطالب
    Route::get('/my_course',[Student_operationController::class,'my_course']);
    //عرض وظائف الطالب لمادة محددة
    Route::get('/my_homework/{subject_id}',[Student_operationController::class,'homework_subject']);
    //عرض برنامج الدوام للطالب
    Route::get('/my_programe',[Student_operationController::class,'programe_week']);
    //عرض الملاحظات الموجهة تجاه الطالب
    Route::get('/my_note',[Student_operationController::class,'display_note']);
    //عرض جميع المناقشات الخاصة بشعبة الطالب فقط عنوان و اسم المدرس
    Route::get('/display_all_post',[StudentPostController::class,'displayAllPost']);
    //عرض مناقشة محددة التعليقات و السؤال
    Route::get('/post/{post_id}',[StudentPostController::class,'displayPost']);
    //إضافة تعليق لمناقشة محددة من قبل طالب أو أستاذ
    Route::post('/add_comment/{post_id}',[StudentPostController::class,'addComment']);
    //حذف تعليق من قبل طالب أو أستاذ الخ مع العلم تعليق الطالب يستطيع أستاذ أو موجه الخ حذفه
    Route::delete('/delete_comment/{comment_id}',[StudentPostController::class,'deleteComment']);
    /*تعديل التعليق لسع مو جاهز للتجريب */
    //تعديل تعليق
    Route::put('/edit_comment/{comment_id}',[StudentPostController::class,'editComment']); 
    //عرض علامات المذاكرة علامات الفحص الخ
    Route::get('/my_mark',[MarkController::class,'displayMark']);
});


// Route::get('/image', function () {
//     $path = storage_path('C:\Users\ASUS\Desktop\AlqemahProject\public\img\xxx.jpg'); // تأكد من تغيير المسار إلى مسار صورتك
//     return response()->file($path);
// });


Route::prefix('parent')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/displayAllBaby',[ParenttController::class,'displayAllBaby']);
});

// route::get('/displayAllBaby',function(){
//     dd('iam here');
// })->middleware('auth:sanctum');









