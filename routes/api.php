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
use App\Http\Controllers\Api_parentt\OutWorkStudentController;
use App\Http\Controllers\Api_parentt\FeeAndPayController;
use App\Http\Controllers\Api_school_monetor\MonetorController;
use App\Http\Controllers\Api_teacher\TeacherController;
use App\Http\Controllers\Api_teacher\PostController;
use App\Http\Controllers\Api_admin\AdminZaController;


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
    //تسجيل طلب للتسجيل بدورة معينة من قبل مستخدم خارجي
    Route::post('/CreateOrderForCourse_out_user/{course_id}',[OrderController::class,'CreateOrderForCourse_out_user']);
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
    //عرض معلومات المعهد
    Route::get('/display_info_academy',[Student_operationController::class,'display_info_academy']);
    //إعلانات المعهد
    Route::get('all_publish', [Student_operationController::class, 'publish']);


});

/*******************************************************all user*******************************************************/
Route::group(['middleware'=>'auth:sanctum'], function(){
    Route::post('/logout', [AdminOperationController::class, 'logout']);
        ///عرض البروفايل
        Route::post('/show_profile',[AuthController::class,'get_profile']);
        ////تعديل البروفايل
        Route::post('/edit_profile{id}',[AuthController::class,'update_profile']);
        //إعلانات المعهد
        Route::get('all_publish', [Student_operationController::class, 'publish']);
        //عرض معلومات المعهد
        Route::get('/display_info_academy',[Student_operationController::class,'display_info_academy']);
});

/*******************************************************admin*******************************************************/
Route::prefix('admin')->middleware(['auth:sanctum','check_admin'])->group(function () {
    ///خلق حسابات الطلاب
    Route::post('/register_student/{order_id}/{academy_id}',[AdminOperationController::class,'register_student']);
    //تسجيل بدون طلب تسجيل
    Route::post('/register_student',[AdminZaController::class,'register']);
    ///خلق حساب للأهل
    Route::post('/register_parentt',[AdminOperationController::class,'register_parentt']);
    //خلق حسابات للاستاذ
    Route::post('/register_teacher',[AdminOperationController::class,'register_teacher']);
    //خلق حسابات للموظف
    Route::post('/register_employee',[AdminOperationController::class,'register_employee']);
    //ايقاف حساب الطالب
    Route::put('/delete_student/{student_id}',[AdminOperationController::class,'delete_student']);
    //ايقاف حساب الاهل
    Route::put('/delete_parentt/{paentt_id}',[AdminOperationController::class,'delete_parentt']);
    //ايقاف حساب الاستاذ
    Route::post('/delete_teacher/{teacher_id}',[AdminOperationController::class,'delete_teacher']);
    //تعديل معلومات موظف
    Route::post('/update_profile_employee/{employee_id}',[AuthController::class,'update_profile_employee']);
    //عرض تصنيف الطلاب
    Route::get('/classification/{calssification}',[AdminOperationController::class,'student_classification']);
    //عرض الطلاب المنتمين للمعهد
    route::get('/desplay_all_student', [AdminZaController::class, 'desplay_all_student_regester']);
    //عرض الصفوف والشعب
    route::get('/desplay_classs_and_section',[AdminOperationController::class,'desplay_classs_and_section']);
    /// عرض البروفايل للطالب
    Route::get('show_profile_student/{student_id}',[AdminOperationController::class,'show_profile_student']);
    //تعديل معلومات الطالب
    Route::post('update_profile_student/{student_id}',[AdminOperationController::class,'update_profile_student']);

    //سجل دوام الطالب
    Route::get('report_for_user_work_on/{student_id}/{year}/{month}',[AdminOperationController::class,'generateMonthlyAttendanceReport']);
    //عرض علامات طالب
    route::get('desplay_student_marks/{student_id}',[AdminZaController::class,'desplay_student_marks']);
    //عرض الملاحظات المقدمة عن الطالب
    Route::get('desplay_student_nots/{student_id}',[AdminZaController::class,'desplay_student_nots']);
    //ارسال انذارات وملاحظات للطالب
    route::post('/create_note/{student_id}', [AdminZaController::class, 'create_note_student']);
    //التعديل على سجل دوام الطالب يعني اضافة غياب او حذف غياب
    //اضافة يوم غياب للطالب
    route::post('/add_student_out_of_work/{student_id}', [AdminOperationController::class, 'addAbsence']);
    //حذف سجل غياب
    Route::delete('delete_student_out_of_work/{student_id}/{absence_id}', [AdminOperationController::class, 'deleteAbsence']);
    //اضافة علامة طالب
    route::post('/add_mark_to_student/{student_id}', [AdminOperationController::class, 'add_mark_to_student']);
    //تعديل علامة طالب
    route::post('/edit_mark_for_student/{student_id}/subject/{subject_id}', [AdminOperationController::class, 'editMark']);
    //عرض كل مدرسي المعهد
    Route::get('/all-teatcher',[AdminOperationController::class,'all_teatcher']);
    // يرجع ايام العطل  مع عدد الاسعات التي غاب فيها  عرض سجل دوام المدرس
    route::get('/get_teacher_schedule_in_mounth/{teacher_id}/{year}/{month}',[AdminOperationController::class,'getteacherworkschedule']);
    //تعديل برنامج دوام المدرس
    route::put('/update_Weekly_Schedule_for_student/{teacher_id}',[AdminOperationController::class,'updateWeeklySchedule']);
    // اضافة يوم غياب للمدرس و الموظف
    Route::post('/add_teachers_and_employee_absence', [AdminOperationController::class, 'addAbsenceForTeacherandemployee']);
    // استعراض راتب المدرس    هذا مو شغال
    Route::get('/desplay_teacher_salary/{teacher_id}/{year}/{month}',[AdminOperationController::class,'desplay_teacher_salary']);
    //عدد ساعات العمل لمدرس
    Route::get('/getworkhour/{teacher_id}/{year}/{month}',[AdminOperationController::class,'getteacherworkhour']);
    //استعراض الدورات التي يعطي فيها مدرس
    route::get('/desplay_teacher_course/{teacher_id}',[AdminOperationController::class,'desplay_teacher_course']);
    //عرض الموظفين
    route::get('/desplay_employee',[AdminOperationController::class,'desplay_employee']);
    //استعراض معلومات موظف
    route::get('/desplay_employee/{employee_id}',[AdminOperationController::class,'desplay_one_employee']);
    //تعديل معلومات موظف
    route::put('/update_employee_profile/{employee_id}/{academy_id}',[AdminOperationController::class,'update_employee_profile']);
    //عرض ايام دوام الموظف
    route::get('/get_monthly_attendance_employee_and_teacher/{year}/{month}',[AdminOperationController::class,'getEmployeeAttendance']);
    //عرض راتب الموظف
    Route::get('/get_employee_salary/{employee_id}',[AdminOperationController::class,'getempoyeesalary']);
    //عرض طلبات التسجيل بالمعهد
    Route::get('/display_order',[AdminOperationController::class,'DisplayOrderNewStudent']);
    //عرض طلبات التسجيل في دورة معينة
    route::get('/display_order_for_course/{course_id}',[AdminOperationController::class,'display_order_for_course']);
    //عرض تفاصيل ورة معينة
    Route::get('/display_details_for_course/{course_id}',[AdminOperationController::class,'display_details_for_course']);
    //عرض الاساتذة في دورة معينة
    Route::get('/display_teacher_in_course/{course_id}',[AdminOperationController::class,'display_teacher_in_course']);
    //عرض المواد التي ستعطى في هذه الدورة
    Route::get('/display_subject_in_course/{course_id}',[AdminOperationController::class,'display_subject_in_course']);
    //اضافة اعلان
    route::post('/add_publish', [AdminZaController::class, 'add_publish']);
    //حذف اعلان
    route::delete('/delete_publish/{publish_id}', [AdminOperationController::class, 'delete_publish']);
    //تعديل اعلان
    route::post('/update_publish/{publish_id}', [AdminOperationController::class, 'update_publish']);
    //اضافة للمصاريف
    route::post('/add_to_expensess', [AdminOperationController::class, 'add_to_expensess']);
    //اضافة للبوفيه
    Route::post('/add_to_break/{academy_id}', [AdminOperationController::class, 'add_to_break']);

    //إعطاء موعد
    route::post('/give_date/{order_id}',[AdminZaController::class,'GiveDate']);
    //إنشاء حساب للطالب
    Route::post('/register/{order_id}/{academy_id}', [AdminOperationController::class, 'registerPost']);
    //حذف طالب


    //انشاء حساب للاستاذ

    //عرض معلومات المدرس
    route::get('/get_teacher_profile/{teacher_id}',[AuthController::class,'get_teacher_profile']);
    //تعديل معلومات المدرس
    route::post('/update_teacher_profile/{teacher_id}',[AuthController::class,'update_teacher_profile']);
    //ادخال برنامج دوام المدرس   /////  استخدمنا واحد غيره  تبع التعديل
    //route::post('/add_Weekly_Schedule_for_teacher/{teacher_id}',[AdminOperationController::class,'addTeacherSchedule']);
    //عرض ايام دوام المدرس
    route::get('/get_monthly_attendance_teacher/{teacher_id}/{year}/{month}',[AdminOperationController::class,'calculatemonthlyattendance']);
    //عرض غيابات المدرس
    route::get('/get_out_of_work_employee/{teacher_id}/{year}/{month}',[AdminOperationController::class,'getteacherabsences']);
    //تقرير مفصل عن كامل ايام الشهر
    route::get('/get_out_of_work_employee_report/{teacher_id}/{year}/{month}',[AdminOperationController::class,'generateMonthlyAttendanceReportReport']);



    //
    route::get('/get_monthly_salary_employee/{employee_id}/{year}/{month}',[AdminOperationController::class,'calculateMonthlySalary']);


    ////  عرض الاعلانات
    route::get('/desplay_publish', [AdminOperationController::class, 'desplay_publish']);
    // //اضافة للارشيف ملفات وصور
    // route::post('/add_files_and_paper', [AdminOperationController::class, 'add_files_and_paper']);
    //إنهاء و إعادة تفعيل مناقشة
    Route::post('off_on_post/{post_id}',[PostController::class,'off_on_post']);



    route::get('/get_out_of_work_employee_report/{teacher_id}/{year}/{month}',[MonetorController::class,'generateMonthlyAttendanceReportReport']);

    //رفع برنامج لشعبة محددة
    Route::post('upload_program_section/{section_id}',[MonetorController::class,'upload_program_section']);
    //حذف برنامج
    Route::delete('delete_program_section/{id}',[MonetorController::class,'delete_program']);
    //تعديل برنامج
    Route::post('update_program_section/{program_id}',[MonetorController::class,'update_program_section']);
    //تعديل معلومات المعهد
    route::post('/edit_info_academy/{id}',[AdminOperationController::class,'edit_info_academy']);
    //الدورات التي سجل فيها الطالب
    route::get('/student_course/{student_id}',[AdminOperationController::class,'student_course']);
    //تعديل السنة الدراسية
    Route::post('edit_year/{id}',[AdminZaController::class,'edit_year']);
    // //عرض طلبات التسجيل في دورة معينة
    // Route::get('order_on_course/{course_id}',[MonetorController::class,'order_on_course']);



    /////////////
    //اضافة سلفة
    Route::post('add_Maturitie',[AdminOperationController::class,'addMaturitie']);
    //حذف سلفة
    Route::delete('delete_Maturitie/{mut_id}',[AdminOperationController::class,'deleteMaturitie']);
    //اضافة دورة
    Route::post('add_course/{academy_id}' ,[AdminOperationController::class,'Add_course']);
    //عرض كل الاعلانات والى اي دورة كل اعلان
    route::get('/desplay_publish', [AdminOperationController::class, 'desplay_all_publish']);
    //هرض اعلان معين مع التفاصيل
    route::get('/desplay_publish/{publish_id}', [AdminOperationController::class, 'desplay_publish']);
    //اضافة ملفات لدورة
    route::post('/upload_file_image_for_course/{course_id}/{academy_id}', [AdminOperationController::class, 'upload_file_image_for_course']);
    //عرض الشعب لصف معين وعرض الطلاب لكل شعبة
    route::get('desplay_section_and_student/{class_id}', [AdminZaController::class, 'desplay_section_and_student']);
    //إضافة دورة za
    Route::post('add_course' ,[AdminZaController::class,'add_course']);
    //عرض تفاصيل دورة za
    route::get('display_info_course/{course_id}', [AdminZaController::class, 'display_info_course']);
    //إضافة موجه za
    Route::post('/add_monetor' ,[AdminZaController::class,'add_monetor']);
    //إضافة محاسب za
    Route::post('/add_accounting',[AdminZaController::class,'add_accounting']);
    //القسط و الدفعات و المتبقي za
    Route::get('/fee/{student_id}',[FeeAndPayController::class,'fee']);
    //عرض كل الموظفين من معليمن وموجهين وووو
    route::get('desplay_all_employee_and_others', [AdminZaController::class, 'desplay_all_employee_and_others']);
    //إضافة دفعة لطالب محدد
    route::post('add_pay/{student_id}', [AdminZaController::class, 'add_pay']);
    //إضافة دفعة لطالب محدد
    route::post('add_pay_course/{student_id}/{course_id}', [AdminZaController::class, 'add_pay_course']);
    //عرض شعب صف معين
    route::get('display_section_for_class/{class_id}', [AdminZaController::class, 'display_section_for_class']);
    //عرض طلاب شعبة معينة
    route::get('display_student_in_section/{section_id}', [AdminZaController::class, 'display_student_in_section']);
    //المبلغ الذي حصل عليه المعهد من دفعات الطلاب للقسط حسب يوم أو شهر أو سنة أو عام دراسي أو دمج بيناتون
    Route::get('/money_from_fee', [AdminZaController::class, 'money_from_fee']);
    //الموافقة على طلب تسجيل في كورس
    route::post('ok_order_course/{order_id}', [AdminZaController::class, 'ok_order_course']);
    //عرض طلبات التسجيل في دورة
    Route::get('/order_on_course/{course_id}', [AdminZaController::class, 'order_on_course']);
    //عرض الطلاب في دورة
    Route::get('/display_student_in_course/{course_id}', [AdminZaController::class, 'display_student_in_course']);
    //رفض طلب تسجيل في دورة
    route::post('no_order_course/{order_id}', [AdminZaController::class, 'no_order_course']);

    Route::get('/money_from_all_course', [AdminZaController::class, 'money_from_all_course']);
    /// اضافة سلفة لاستاذ
    Route::post('/add_teacher_maturitie/{idteacher}',[AdminZaController::class,'addTeacherMaturitie']);
    /// اضافة سلفة لموظف
    Route::post('/add_employee_maturitie/{idemployee}',[AdminZaController::class,'addEmployeeMaturitie']);
    //عرض برنامج دوام شعبة
    Route::get('/programe_week/{section_id}', [AdminZaController::class, 'programe_week']);
    //عرض برنامج الدوام الاسبوعي للاستاذ
    route::get('/getWeeklyTeacherSchedule/{teacher_id}',[AdminZaController::class,'getWeeklyTeacherSchedule']);
    //عرض السلف والباقي من الراتب والراتب للاستاذ
    route::get('desplay_maturitie_for_teacher/{teacher_id}/{year}/{month}', [AdminOperationController::class, 'desplay_maturitie_for_teacher']);
    //عرض السلف والباقي من الراتب والراتب للموظف
    route::get('desplay_maturitie_for_employee/{employee_id}/{year}/{month}', [AdminOperationController::class, 'desplay_maturitie_for_employee']);

    //اضافة علامة جزئية معينة لمادة معينة لطلاب شعبة
    Route::put('/add_marks_to_section/{section_id}',[AdminOperationController::class,'add_marks_to_section']);
    //لمعرفة هل الطالب ناجح ام راسب في مادة معينة
    Route::post('/calculateStudentMarks/{student_id}',[AdminOperationController::class,'calculateStudentMarks']);
    //لمعرفة هل الطالب ناجح ام راسب في جميع المواد ة
    Route::get('/calculateAllStudentMarks/{student_id}',[AdminOperationController::class,'calculateAllStudentMarks']);


    //اضافة شعبة لصف معين
    route::post('/add_section_for_class/{class_id}', [AdminOperationController::class, 'add_section']);
    //عرض الاساتذة في دورة معينة
    Route::get('/display_teacher_in_course/{course_id}',[AdminZaController::class,'display_teacher_in_course']);
    //تسجيل طلب للتسجيل بدورة معينة
    Route::post('/add-order-course/{course_id}',[OrderController::class,'CreateOrderForCourse']);
    //عرض كل مناقشات شعبة محددة
    Route::get('/display_post/{section_id}', [AdminZaController::class, 'display_post']);
    //عرض مناقشة محددة التعليقات و السؤال
    Route::get('/post/{post_id}',[StudentPostController::class,'displayPost']);
    //عرض أساتذة مادة محددة لصف محدد من أجل دورة
    Route::get('/display_teacher_for_course/{name_subject}/{class_id}',[AdminZaController::class,'display_teacher_for_course']);
    //عرض ملفات و صور دورة محددة
    Route::get('/display_file_course/{course_id}',[Student_operationController::class,'display_file_course']);
    //عرض كل الدورات التي لها طلبات تسجيل لم تحدد حالتها بالرفض أو القبول
    Route::get('/all_course_have_order_yes_no',[AdminZaController::class,'all_course']);
    //البحث عن طالب ضمن كل طلاب العام الدراسي
    Route::get('/search_student',[AdminZaController::class,'search_student']);
    //البحث عن أستاذ ضمن كل طلاب العام الدراسي
    Route::get('/search_teacher',[AdminZaController::class,'search_teacher']);
    //البحث عن لموظفين ضمن كل طلاب العام الدراسي
    Route::get('/search_employee',[AdminZaController::class,'search_employee']);
    //البحث عن طالب ضمن طلاب شعبة محددة
    Route::get('/search_student_in_section/{section_id}',[AdminZaController::class,'search_student_in_section']);
    Route::get('/all_expenses', [AdminZaController::class, 'all_expenses']);
    Route::get('/all_Maturitie', [AdminZaController::class, 'all_Maturitie']);
    Route::get('/all_salary_employees', [AdminZaController::class, 'all_salary_employees']);
    Route::get('/salary_all', [AdminZaController::class, 'salary_all']);
    Route::get('/all_salary_employees_teacher', [AdminZaController::class, 'all_salary_employees_teacher']);
    Route::get('/desplay_employee_salary/{employee_id}',[AdminZaController::class,'desplay_employee_salary']);
    //عرض جميع صفوف المعهد
    Route::get('/display_all_class',[AdminZaController::class,'display_all_class']);
    //عرض شعب مدرس حسب صف محدد
    Route::get('/display_section_for_class_teacher/{class_id}/{teacher_id}',[AdminZaController::class,'display_section_for_class_teacher']);
    //عرض طلاب سنة محددة
    Route::get('/desplay_all_student_regester_in_year/{year}',[AdminZaController::class,'desplay_all_student_regester_in_year']);
    //
    Route::get('/desplay_all_teacher_regester_in_year/{year}',[AdminZaController::class,'desplay_all_teacher_regester_in_year']);


    Route::delete('/delete_comment/{comment_id}',[PostController::class,'deleteComment']);
    //لاضافة الساعات الاضافية
    Route::post('/add_extrahour/{teacher_id}',[AdminOperationController::class,'add_extrahour']);
    //لتعديل الساعات الاضافية
    Route::put('/update_extrahour/{hourid}',[AdminOperationController::class,'update_extrahour']);
    //لحذف الساعات الاضافية
    Route::delete('/delete_extrahour/{teacher_id}/{hour_id}',[AdminOperationController::class,'delete_extrahour']);
    Route::get('/all_salary',[AdminZaController::class,'all_salary']);
    Route::get('/calculate_balance',[AdminZaController::class,'calculate_balance']);
    Route::get('/showUserActivities',[AdminZaController::class,'showUserActivities']);
    Route::get('/all_action_for_user/{user_id}',[AdminZaController::class,'all_action_for_user']);
    
    
});

/*******************************************************monetor*******************************************************/
Route::prefix('monetor')->middleware(['auth:sanctum','ckeck_monetor','logUserActivity'])->group(function(){
    //عرض تصنيف الطلاب
    Route::get('/classification/{classification/}',[MonetorController::class,'student_classification']);
    //عرض الطلاب المنتمين للمعهد
    route::get('/desplay_all_student/{year}', [MonetorController::class, 'desplay_all_student_regester']);
    //عرض الصفوف والشعب
    route::get('/desplay_classs_and_section',[MonetorController::class,'desplay_classs_and_section']);
    /// عرض البروفايل للطالب
    Route::get('show_profile_student/{student_id}',[MonetorController::class,'show_profile_student']);
    //تعديل معلومات الطالب
    Route::post('update_profile_student/{student_id}',[AdminOperationController::class,'update_profile_student']);
    //سجل دوام الطالب
    Route::get('report_for_user_work_on/{student_id}/{year}/{month}',[AdminOperationController::class,'generateMonthlyAttendanceReport']);

    //عرض علامات طالب
    route::get('desplay_student_marks/{student_id}',[AdminZaController::class,'desplay_student_marks']);
    //عرض الملاحظات تجاه الطال
    route::get('desplay_student_note/{student_id}',[MonetorController::class,'desplay_student_note']);
    //ارسال انذارات وملاحظات للطالب
    route::post('/create_note/{student_id}', [AdminZaController::class, 'create_note_student']);
    //عرض كل مدرسي المعهد
    Route::get('/all-teatcher',[AdminOperationController::class,'all_teatcher']);

    //عرض معلومات مدرس معين
    Route::get('/info-teatcher/{teatcher_id}',[MonetorController::class,'info_teatcher']);
    //استعراض الدورات التي يعطي فيها مدرس
    route::get('/desplay_teacher_course/{teacher_id}',[AdminOperationController::class,'desplay_teacher_course']);

    //  تعديل برنامج دوام المدرس   //  صار الكل بالكل للاضافة والتعديل
    route::put('/update_Weekly_Schedule_for_student/{teacher_id}',[MonetorController::class,'updateWeeklySchedule']);
    //عرض سجل دوام المدرس
    route::get('/get_teacher_schedule_in_mounth/{teacher_id}/{year}/{month}',[MonetorController::class,'getteacherworkschedule']);
    //عرض ايام دوام المدرس
    route::get('/get_monthly_attendance_teacher/{teacher_id}/{year}/{month}',[MonetorController::class,'calculatemonthlyattendance']);
    //عرض غيابات المدرس
    route::get('/get_out_of_work_employee/{teacher_id}/{year}/{month}',[MonetorController::class,'getteacherabsences']);
    //تقرير مفصل عن كامل ايام الشهر
    route::get('/get_out_of_work_employee_report/{teacher_id}/{year}/{month}',[AdminOperationController::class,'generateMonthlyAttendanceReportReport']);

    route::get('/get_out_of_work_employee_report/{teacher_id}/{year}/{month}',[MonetorController::class,'generateMonthlyAttendanceReportReport']);

    //عرض معلومات دورة معينة
    Route::get('/info_course/{id_course}',[MonetorController::class,'info_course']);
    ////  عرض الاعلانات
    route::get('/desplay_publish', [MonetorController::class, 'desplay_all_publish']);//استخدم بدل عنه تبع كل المستخدمين
    //عرض طلبات التسجيل بالمعهد
    Route::get('/display_order',[MonetorController::class,'DisplayOrderNewStudent']);
    // //عرض طلبات التسجيل في دورة معينة
    // route::get('/display_order_for_course/{course_id}',[MonetorController::class,'display_order_for_course']);
    //اضافو اعلان
    route::post('/add_publish', [AdminZaController::class, 'add_publish']);
    //حذف اعلان
    route::delete('/delete_publish/{publish_id}', [MonetorController::class, 'delete_publish']);
    //تعديل اعلان
    route::post('/update_publish/{publish_id}', [AdminOperationController::class, 'update_publish']);
    //اضافة علامة طالب
    route::post('/add_mark_to_student/{student_id}', [MonetorController::class, 'add_mark_to_student']);
    //تعديل علامة طالب
    route::post('/edit_mark_for_student/{student_id}/subject/{subject_id}', [MonetorController::class, 'editMark']);
    //اضافة يوم غياب للطالب
    route::post('/add_student_out_of_work/{student_id}', [AdminOperationController::class, 'addAbsence']);

    //إنهاء و إعادة تفعيل مناقشة
    Route::post('off_on_post/{post_id}',[PostController::class,'off_on_post']);

    //عرض طلبات التسجيل في دورة معينة
    Route::get('order_on_course/{course_id}',[AdminZaController::class,'order_on_course']);
    //رفع برنامج لشعبة محددة
    Route::post('upload_program_section/{section_id}',[MonetorController::class,'upload_program_section']);
    //حذف برنامج
    Route::delete('delete_program_section/{id}',[MonetorController::class,'delete_program']);

    //تعديل برنامج
    Route::post('update_program_section/{program_id}',[MonetorController::class,'update_program_section']);
    //عرض الملاحظات المقدمة عن الطالب
    Route::get('desplay_student_nots/{student_id}',[AdminZaController::class,'desplay_student_nots']);
    //الدورات التي سجل فيها الطالب
    route::get('/student_course/{student_id}',[AdminOperationController::class,'student_course']);
    //عرض شعب صف معين
    route::get('display_section_for_class/{class_id}', [AdminZaController::class, 'display_section_for_class']);
    //عرض طلاب شعبة معينة
    route::get('display_student_in_section/{section_id}', [AdminZaController::class, 'display_student_in_section']);






    //تعديل معلومات موظف
    Route::post('/update_profile_employee/{employee_id}',[AuthController::class,'update_profile_employee']);

    //تعديل معلومات المدرس
    route::post('/update_teacher_profile/{teacher_id}',[AuthController::class,'update_teacher_profile']);

    //اضافة ملفات لدورة
    route::post('/upload_file_image_for_course/{course_id}/{academy_id}', [AdminOperationController::class, 'upload_file_image_for_course']);

    //عرض تفاصيل دورة za
    route::get('display_info_course/{course_id}', [AdminZaController::class, 'display_info_course']);
    //عرض كل الموظفين من معليمن وموجهين وووو
    route::get('desplay_all_employee_and_others', [AdminZaController::class, 'desplay_all_employee_and_others']);
    //الموافقة على طلب تسجيل في كورس
    route::post('ok_order_course/{order_id}', [AdminZaController::class, 'ok_order_course']);
    //عرض الطلاب في دورة
    Route::get('/display_student_in_course/{course_id}', [AdminZaController::class, 'display_student_in_course']);
    //عرض جميع الدورات الموجودة بالمعهد
    Route::get('/all_course',[DisplayController::class,'all_course']);

    //رفض طلب تسجيل في دورة
    route::post('no_order_course/{order_id}', [AdminZaController::class, 'no_order_course']);

    //عرض برنامج دوام شعبة
    Route::get('/programe_week/{section_id}', [AdminZaController::class, 'programe_week']);
    //عرض برنامج الدوام الاسبوعي للاستاذ
    route::get('/getWeeklyTeacherSchedule/{teacher_id}',[AdminZaController::class,'getWeeklyTeacherSchedule']);
    //تسجيل طلب للتسجيل بدورة معينة
    Route::post('/add-order-course/{course_id}',[OrderController::class,'CreateOrderForCourse']);
    //عرض كل مناقشات شعبة محددة
    Route::get('/display_post/{section_id}', [AdminZaController::class, 'display_post']);
    //عرض مناقشة محددة التعليقات و السؤال
    Route::get('/post/{post_id}',[StudentPostController::class,'displayPost']);
    //عرض ملفات و صور دورة محددة
    Route::get('/display_file_course/{course_id}',[Student_operationController::class,'display_file_course']);
    //عرض كل الدورات التي لها طلبات تسجيل لم تحدد حالتها بالرفض أو القبول
    Route::get('/all_course_have_order_yes_no',[AdminZaController::class,'all_course']);
    //عرض مواد الطالب
    Route::get('/subject/{class_id}',[AdminZaController::class,'display_subject_for_class']);
    //عرض صور مواد الطالب
    Route::get('/img_subject/{subject_id}',[Student_operationController::class,'display_img_subject']);
    //عرض الملفات للمادة المختارة
    Route::get('/file_subject/{subject_id}',[Student_operationController::class,'display_file_subject']);
    //عرض ملفات مادة محددة حسب سنة محددة
    Route::get('/file_subject_year/{subject_id}/{year}',[Student_operationController::class,'file_subject_year']);
    //عرض صور مادة محددة حسب سنة محددة
    Route::get('/img_subject_year/{subject_id}/{year}',[Student_operationController::class,'img_subject_year']);
    //رفع ملف أو صورة لملفات السنة الحاليةzahraa
    Route::post('/upload_file_image/{subject_id}',[TeacherController::class,'upload_file_image']);
    //رفع ملف أو صورة لملفات الأرشيفzahraa
    Route::post('/upload_file_image_archive/{archive_id}',[TeacherController::class,'upload_file_image_archive']);
    //حذف ملف أو صورة
    Route::delete('/delete_file_image/{file_img_id}/{imgFileName}',[TeacherController::class,'delete_file_image']);
    //تعديل ملف أو صورة
    Route::post('/update-file-image/{id}', [TeacherController::class,'update_file_image']);
    //عرض طلبات التسجيل بالمعهد
    Route::get('/display_order',[AdminOperationController::class,'DisplayOrderNewStudent']);
    //إعطاء موعد
    route::post('/give_date/{order_id}',[AdminZaController::class,'GiveDate']);
    //البحث عن طالب ضمن كل طلاب العام الدراسي
    Route::get('/search_student',[AdminZaController::class,'search_student']);
    //البحث عن أستاذ ضمن كل أساتذة العام الدراسي
    Route::get('/search_teacher',[AdminZaController::class,'search_teacher']);
    //البحث عن لموظفين ضمن كل موظفي العام الدراسي
    Route::get('/search_employee',[AdminZaController::class,'search_employee']);
    //البحث عن طالب ضمن طلاب شعبة محددة
    Route::get('/search_student_in_section/{section_id}',[AdminZaController::class,'search_student_in_section']);
    //عرض جميع صفوف المعهد
    Route::get('/display_all_class',[AdminZaController::class,'display_all_class']);
    //عرض شعب مدرس حسب صف محدد
    Route::get('/display_section_for_class_teacher/{class_id}/{teacher_id}',[AdminZaController::class,'display_section_for_class_teacher']);
    //تعديل برنامج دوام المدرس
    route::put('/update_Weekly_Schedule_for_student/{teacher_id}',[AdminOperationController::class,'updateWeeklySchedule']);
    //عرض السنوات التي تحتوي ملفات للأرشيف حسب المادة
    Route::get('/display_year_archive/{subject_id}',[Student_operationController::class,'display_year_archive']);


    Route::delete('/delete_comment/{comment_id}',[PostController::class,'deleteComment']);

});

/*******************************************************student*******************************************************/
Route::prefix('student')->middleware(['auth:sanctum','ckeck_student'])->group(function () {
    //عرض مواد الطالب
    Route::get('/my_subject',[Student_operationController::class,'display_subject']);
    //عرض صور مواد الطالب
    Route::get('/img_subject/{subject_id}',[Student_operationController::class,'display_img_subject']);
    //عرض الملفات للمادة المختارة
    Route::get('/file_subject/{subject_id}',[Student_operationController::class,'display_file_subject']);
    //تسجيل طالب في دورة
    Route::post('/create-order-course/{course_id}',[Student_operationController::class,'orderCourse']);
    //الكورسات يلي مسجل فيها الطالب
    Route::get('/my_course',[Student_operationController::class,'my_course']);
    //عرض وظائف الطالب لمادة محددة
    Route::get('/my_homework/{subject_id}',[Student_operationController::class,'homework_subject']);
    //عرض ملحقات وظيفة محددة
    Route::get('/file_image_homework/{homework_id}',[Student_operationController::class,'file_image_homework']);
    //عرض برنامج الدوام للطالب
    Route::get('/my_programe',[Student_operationController::class,'programe_week']);
    //عرض السنوات التي تحتوي ملفات للأرشيف حسب المادة
    Route::get('/display_year_archive/{subject_id}',[Student_operationController::class,'display_year_archive']);
    //عرض ملفات مادة محددة حسب سنة محددة
    Route::get('/file_subject_year/{subject_id}/{year}',[Student_operationController::class,'file_subject_year']);
    //عرض صور مادة محددة حسب سنة محددة
    Route::get('/img_subject_year/{subject_id}/{year}',[Student_operationController::class,'img_subject_year']);
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
    //تعديل تعليق
    Route::post('/edit_comment/{comment_id}',[StudentPostController::class,'editComment']);
    //عرض علامات المذاكرة علامات الفحص الخ
    Route::get('/my_mark',[MarkController::class,'displayMark']);
    //عرض الإعلانات
    //استخدمت تبع كل المستخدمين
    route::get('/publish',[Student_operationController::class,'publish']);
    //عرض كل كورسات المعهد
    Route::get('/all_course',[DisplayController::class,'all_course']);
    //عرض معلومات دورة معينة
    Route::get('/info_course/{id_course}',[DisplayController::class,'info_course']);
    //عرض معلومات الطالب و صورته
    Route::get('/show_my_profile',[Student_operationController::class,'show_my_profile']);
    //تعديل معلومات الطالب الرقم العنوان و كلمة السر
    Route::post('/edit_some_info_profile',[Student_operationController::class,'edit_some_info_profile']);
    //عرض ملفات و صور دورة محددة
    Route::get('/display_file_course/{course_id}',[Student_operationController::class,'display_file_course']);


});

/*******************************************************parent*******************************************************/
Route::prefix('parent')->middleware(['auth:sanctum'])->group(function () {
    //عرض جميع أبنائي المسجلين بالمعهد
    Route::get('/displayAllBaby',[ParenttController::class,'displayAllBaby']);
    //برنامج الدوام الخاص بالابن المحدد
    Route::get('/display_Programe_my_sun/{student_id}',[ParenttController::class,'programe_week']);
    //عرض مواد ابني
    Route::get('/display_Subject_Sun/{student_id}',[ParenttController::class,'displaySubjectSun']);
    //عرض وظائف ابني لمادة محددة
    Route::get('/display_homework_Sun/{student_id}/{subject_id}',[ParenttController::class,'homework_subject_my_sun']);
    //عرض ملحقات وظيفة محددة
    Route::get('/file_image_homework/{homework_id}',[Student_operationController::class,'file_image_homework']);
    //عرض كل غيابات الابن
    Route::get('/all_out_work_student/{student_id}', [OutWorkStudentController::class, 'all_out_work_student']);
    //إضافة تبرير للابن لغيابه في يوم محدد
    Route::post('/add_Justification/{Out_Of_Work_Student_id}', [OutWorkStudentController::class, 'add_Justification']);
    //عرض الملاحظات التي بحق الابن
    Route::get('/display_note/{student_id}',[ParenttController::class,'display_note']);
    //عرض علامات الابنREZ
    Route::get('/display_mark/{student_id}',[ParenttController::class,'displayMark']);
    //القسط و الدفعات و المتبقي
    Route::get('/fee/{student_id}',[FeeAndPayController::class,'fee']);
    //تعديل معلومات الأهل الرقم العنوان و كلمة السر
    Route::post('/edit_some_info_profile_parent',[ParenttController::class,'edit_some_info_profile_parent']);

});

/*******************************************************teacher*******************************************************/
Route::prefix('teacher')->middleware(['auth:sanctum','check_teacher'])->group(function () {
    //عرض برنامج الدوام الأستاذ
    Route::get('/my_programe_teacher',[TeacherController::class,'programe']);
    //إضافة ملاحظات لطالب معين
    Route::post('/add_note_about_student/{student_id}',[TeacherController::class,'add_note_about_student']);
    //غيابات المدرس
    Route::get('/out_work_teacher',[TeacherController::class,'out_work_teacher']);
    //عرض المواد التي أدرسها
    Route::get('/display_supject',[TeacherController::class,'display_supject']);
    //عرض المواد مع الصف التي أدرسها
    Route::get('/display_supject_with_class',[TeacherController::class,'display_supject_with_class']);
    //عرض ملفات المواد التي أدرسها
    Route::get('/display_file_subject_teacher/{subject_id}',[TeacherController::class,'display_file_subject']);
    //عرض صور المواد التي أدرسها
    Route::get('/display_img_subject_teacher/{subject_id}',[TeacherController::class,'display_img_subject']);
    //عرض السنوات التي تحتوي ملفات للأرشيف حسب المادة
    Route::get('/display_year_archive/{subject_id}',[TeacherController::class,'display_year_archive']);
    //عرض ملفات مادة محددة حسب سنة محددة
    Route::get('/file_subject_year/{subject_id}/{year}',[TeacherController::class,'file_subject_year']);
    //عرض ملفات مادة محددة حسب سنة محددة
    Route::get('/img_subject_year/{subject_id}/{year}',[TeacherController::class,'img_subject_year']);
    //المواد و الصف الذي يعطيها المدرس
    Route::get('/classs',[TeacherController::class,'classs']);
    //الشعب التي يعطيها المدرس حسب الصف
    Route::get('/section/{class_id}', [TeacherController::class,'suction']);
    //عرض طلاب شعبة محددة
    Route::get('/display_student_section/{section_id}',[TeacherController::class,'display_student_section']);
    //عرض معلومات طالب
    Route::get('display_info_student/{student_id}', [TeacherController::class,'display_info_student']);
    //عرض علامات طالب لمادة محددة حسب المادة التي يعطيها المدرس
    Route::get('/display_mark/{student_id}', [TeacherController::class,'display_mark']);
    //تعديل علامة طالب
    Route::post('/edit_mark/{mark_id}', [TeacherController::class,'edit_mark']);
    //إنشاء مناقشة لشعبة محددة
    Route::post('/create_post/{section_id}',[PostController::class,'create_post']);
    //عرض المدرس لمناقشاته
    Route::get('/display_post',[PostController::class,'display_post']);
    //عرض مناقشة محددة التعليقات و السؤال
    Route::get('/post/{post_id}',[PostController::class,'displayPost']);
    //إضافة تعليق لمناقشة محددة من قبل طالب أو أستاذ
    Route::post('/add_comment/{post_id}',[PostController::class,'addComment']);
    //تعديل تعليق
    Route::post('/edit_comment/{comment_id}',[PostController::class,'editComment']);
    //حذف تعليق من قبل طالب أو أستاذ الخ مع العلم تعليق الطالب يستطيع أستاذ أو موجه الخ حذفه
    Route::delete('/delete_comment/{comment_id}',[PostController::class,'deleteComment']);
    //إنهاء مناقشة
    Route::post('off_on_post/{post_id}',[PostController::class,'off_on_post']);
    //عرض غيابات طالب معين
    Route::get('/all_out_work_student/{student_id}', [OutWorkStudentController::class, 'all_out_work_student']);
    //سجل دوام الطالب
    Route::get('report_for_user_work_on/{student_id}/{year}/{month}',[AdminOperationController::class,'generateMonthlyAttendanceReport']);
    //عرض كل الطلاب الذين أدرسهم
    Route::get('/display_all_students_I_teach',[TeacherController::class,'display_all_students_I_teach']);
    //رفع ملف أو صورة لملفات السنة الحاليةzahraa
    Route::post('/upload_file_image/{subject_id}',[TeacherController::class,'upload_file_image']);
    //رفع ملف أو صورة لملفات الأرشيفzahraa
    Route::post('/upload_file_image_archive/{archive_id}',[TeacherController::class,'upload_file_image_archive']);
    Route::delete('/delete_file_image/{file_img_id}/{imgFileName}',[TeacherController::class,'delete_file_image']);
    Route::post('/update-file-image/{id}', [TeacherController::class,'update_file_image']);

});



// Route::get('/image', function () {
//     $path = storage_path('C:\Users\ASUS\Desktop\AlqemahProject\public\img\xxx.jpg'); // تأكد من تغيير المسار إلى مسار صورتك
//     return response()->file($path);
// });




Route::post('/create',[AuthController::class,'create']);
Route::get('/get',[AuthController::class,'get']);
Route::patch('/edit/{id}',[AuthController::class,'edit']);

//khjjj


Route::post('/upload',[TeacherController::class,'upload']);
Route::get('/upload/{imageName}', [TeacherController::class, 'showImage']);
Route::get('/listImages', [TeacherController::class, 'listImages']);

Route::delete('/delete/{id}', [TeacherController::class, 'delete']);

