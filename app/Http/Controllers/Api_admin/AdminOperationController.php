<?php

namespace App\Http\Controllers\Api_admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Appointment;
use App\Models\Breake;
use App\Models\Classs;
use App\Models\Student;
use App\Models\Parentt;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Employee;
use App\Models\Expenses;
use App\Models\Mark;
use Illuminate\Support\Str;
use App\Models\Note;
use App\Models\Note_Student;
use App\Models\Publish;
use Illuminate\Support\Carbon;
use App\Models\Out_Of_Work_Employee;
use App\Models\Out_Of_Work_Student;
use App\Models\Teacher_Schedule;
use App\Models\Out__Of__Work__Employee;
use App\Models\Image;
use App\Models\Academy;
use App\Models\Subject;


class AdminOperationController extends BaseController
{

//     public function login(Request $request)
//     {
//         $request->validate([
//             "email" => "required|email",
//             "password" => "required"
//         ]);

//         // check email
//         $user = User::where("email", "=", $request->email)->where('status' , 1);
// if($user){
//     $user = User::where("email", "=", $request->email)->first();
//     if($user->status == 0){
//     if(isset($user->id)){
//         if(Hash::check($request->password, $user->password)){
//             // create a token
//             $token = $user->createToken("auth_token")->plainTextToken;
//             /// send a response
//             return response()->json([
//         'User login successfully',
//         'token'=>$token,
//     ]);
//         }
//     }
// }
//     else{
//         $parent = Parentt::where("email", "=", $request->email)->first();
//     if(isset($parent->id)){
//         if(Hash::check($request->password, $parent->password)){
//             // create a token
//             $token = $parent->createToken("auth_token")->plainTextToken;
//             /// send a response
//             return response()->json([
//         'User login successfully',
//         'token'=>$token,
//     ]);
//         }
//     }else{
//         return $this->responseError(['please  check your Auth','auth error']);
//     }

//     }
//     return $this->responseError(['please  check your Auth','auth error']);
// }
//     }
public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        // check email
        $user = User::where("email", "=", $request->email);
if($user){
    $user = User::where("email", "=", $request->email)->first();
    if(isset($user->id)){
        if(Hash::check($request->password, $user->password)){
            // create a token
            $token = $user->createToken("auth_token")->plainTextToken;
            /// send a response
            return response()->json([
        'User login successfully',
        'token'=>$token,
    ]);
        }
    }else{
        $parent = Parentt::where("email", "=", $request->email)->first();
    if(isset($parent->id)){
        if(Hash::check($request->password, $parent->password)){
            // create a token
            $token = $parent->createToken("auth_token")->plainTextToken;
            /// send a response
            return response()->json([
        'User login successfully',
        'token'=>$token,
    ]);
        }
    }else{
        return $this->responseError(['please  check your Auth','auth error']);
    }

    }
    return $this->responseError(['please  check your Auth','auth error']);
}
    }

    public function logout(Request $request)
    {
        if(Auth::check()){
            $user = User::where("email", auth()->user()->email);
            //$parentt = Parentt::where("email", auth()->parentt()->email);
            if($user){
                $request->user()->currentAccessToken()->delete();
                return response()->json(['status' => true, 'message' => 'User logged out successfully'], 200);
            }
            elseif ($parentt) {
                $request->parentt()->currentAccessToken()->delete();
                return response()->json(['status' => true, 'message' => 'User logged out successfully'], 200);
            }
            else {
                return response()->json(['status' => false, 'message' => 'User not found'], 404);
            }
    }
    else {
                return response()->json(['status' => false, 'message' => 'User not authenticated'], 401);
            }
    }
//     public function logout(Request $request)
// {
//     if(Auth::check()){
//         $user = User::where("email", auth()->user()->email)->first();
//         if($user){
//             $request->user()->currentAccessToken()->delete();
//             return response()->json(['status' => true, 'message' => 'User logged out successfully'], 200);
//         } else {
//             return response()->json(['status' => false, 'message' => 'User not found'], 404);
//         }
//     } else {
//         return response()->json(['status' => false, 'message' => 'User not authenticated'], 401);
//     }
// }




    public function register_student(Request $request, $order_id)
{

    // جلب الطلب حسب المعرف
    $order = Order::find($order_id);
    // التحقق من صحة البيانات الأولية
    $user = new User();
        $email = $order->first_name . Str::random(5) . "@gmail.com";
        $password = $order->first_name . Str::random(6);
        $user->first_name = $order->first_name;
        $user->last_name = $order->last_name;
        $user->father_name = $order->father_name;
        $user->mother_name = $order->mother_name;
        $user->birthday = $order->birthday;
        $user->gender = $order->gender;
        $user->phone = $order->phone;
        $user->address = $order->address;
        $user->year = $order->year;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->conf_password = Hash::make($password);
        $user->user_type = 'student';
        $user->save();


    // إنشاء البريد الإلكتروني وكلمة المرور تلقائيًا


    // إنشاء المستخدم الجديد



    // التحقق من صحة البيانات الثانوية
    $validator1 = Validator::make($request->all(), [
        'school_tuition' => 'required',
        'class_id' => 'required',
        'section_id' => 'required',
        'parentt_id' => 'required',
    ]);

    if ($validator1->fails()) {
        return $this->responseError(['errors' => $validator1->errors()]);
    }

    // إنشاء سجل الطالب الجديد
    $student = new Student();
    $student->school_tuition = $request->school_tuition;
    $student->user_id = $user->id;
    $student->class_id = $request->class_id;
    $student->section_id = $request->section_id;
    $student->parentt_id = $request->parentt_id;
    $student->student_type = $order->student_type ?: $request->student_type;

    // تعيين التصنيف إذا كان الطالب من فئة البكالوريا
    if ($request->student_type == 0 ) {
        $validator2 = Validator::make($request->all(), [
            'calssification' => 'required|in:0,1', // 0 للعلمي، 1 للأدبي
        ]);

        if ($validator2->fails()) {
            return $this->responseError(['errors' => $validator2->errors()]);
        }

        $student->calssification = $request->calssification;
    } else {
        $student->calssification = null;
    }

    $student->save();

    // إرجاع بيانات الدخول
    return response()->json([$user->email, $password]);
}


public function register_student1(Request $request){


        $validator3 = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required|string',
            'father_name' => 'required|string',
            'mother_name' => 'required|string',
            'birthday' => 'required|date',
            'gender'=>'required',
            'year'=>'required',
            'phone' => 'required',
            'address' => 'required',
            'email'=>'required|email',
            'password' => 'required|min:8',
            'conf_password' => 'required|min:8',
        ]);

        if ($validator3->fails()) {
            return $this->responseError(['errors' => $validator3->errors()]);
        }

        $user = new User();

        $password  = $request->password;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->father_name = $request->father_name;
        $user->mother_name = $request->mother_name;
        $user->birthday = $request->birthday;
        $user->gender = $request->gender;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->year = $request->year;
        $user->email = $request->email;
        $user->password = Hash::make($password);
        $user->conf_password = Hash::make($password);
        $user->user_type = 'student';
        $user->save();

        $validator1 = Validator::make($request->all(), [
            'school_tuition' => 'required',
            'class_id' => 'required',
            'section_id' => 'required',
            'parentt_id' => 'required',
            'student_type'=>'required',
        ]);

        if ($validator1->fails()) {
            return $this->responseError(['errors' => $validator1->errors()]);
        }

        // إنشاء سجل الطالب الجديد
        $student = new Student();
        $student->school_tuition = $request->school_tuition;
        $student->user_id = $user->id;
        $student->class_id = $request->class_id;
        $student->section_id = $request->section_id;
        $student->parentt_id = $request->parentt_id;
        $student->student_type = $request->student_type;

        // تعيين التصنيف إذا كان الطالب من فئة البكالوريا
        if ($request->student_type == 0 ) {
            $validator2 = Validator::make($request->all(), [
                'calssification' => 'required|in:0,1', // 0 للعلمي، 1 للأدبي
            ]);

            if ($validator2->fails()) {
                return $this->responseError(['errors' => $validator2->errors()]);
            }

            $student->calssification = $request->calssification;
        } else {
            $student->calssification = null;
        }

        $student->save();

        // إرجاع بيانات الدخول
        return response()->json([$user->email, $password]);
    }







    public function register_parentt(Request $request){

        $validator = Validator::make($request->all(),[
            'first_name'=>'required|string',
            'last_name' => 'required|string',
            'phone' => 'required',
            'address' => 'required',
            'email'=>'required|email',
            'password' => 'required|min:8',
            'conf_password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return $this->responseError(['errors' => $validator->errors()]);
        }

        $parentt =new Parentt();
        $parentt->first_name = $request->first_name;
        $parentt->last_name = $request->last_name;
        $parentt->phone = $request->phone;
        $parentt->address = $request->address;
        $parentt->email = $request->email;
        $parentt->password = Hash::make($request->password);
        $parentt->conf_password = Hash::make($request->conf_password);

        $parentt->save();
        return response()->json([$parentt->email, $parentt->password]);


    }

public function register_teacher(Request $request)
{
    $validator = Validator::make($request->all(), [
        'first_name' => 'required',
        'last_name' => 'required',
        'father_name' => 'required',
        'mother_name' => 'required',
        'birthday' => 'required',
        'gender' => 'required',
        'phone' => 'required',
        'address' => 'required',
        'year' => 'required',
        'image' => 'required',
    ]);


    if ($validator->fails()) {
        return $this->responseError(['errors' => $validator->errors()]);
    }

    $password = $request->first_name . Str::random(4) ;

    $user = new User();
    $user->first_name = $request->first_name;
    $user->last_name = $request->last_name;
    $user->father_name = $request->father_name;
    $user->mother_name = $request->mother_name;
    $user->birthday = $request->birthday;
    $user->gender = $request->gender;
    $user->phone = $request->phone;
    $user->address = $request->address;
    $user->year = $request->year;
    $user->image = $request->image;
    $user->email = $request->first_name . Str::random(5) . "@gmail.com";
    $user->password = Hash::make($password);
    $user->conf_password = Hash::make($password);
    $user->user_type = 'teacher';



    $user->save();


    $validator1 = Validator::make($request->all(), [
        'cost_hour' => 'required',
        'num_hour_added' => 'required',
        'note_hour_added' => 'required',
    ]);

    if ($validator1->fails()) {
        return $this->responseError(['errors' => $validator1->errors()]);
    }


    $teacher = new Teacher();
    $teacher->cost_hour = $request->cost_hour;
    $teacher->num_hour_added = $request->num_hour_added;
    $teacher->note_hour_added = $request->note_hour_added;
    $teacher->user_id = $user->id;

    $teacher->save();
    return response()->json([$user->email, $password]);

    }

public function register_employee(Request $request)
{
    $validator = Validator::make($request->all(), [
        'first_name' => 'required',
        'last_name' => 'required',
        'phone' => 'required',
        'address' => 'required',
        'salary' => 'required',
        'year' => 'required',
        'type'=>'required',
    ]);

    if ($validator->fails()) {
        return $this->responseError(['errors' => $validator->errors()]);
    }

    $email = $request->first_name . Str::random(5) . "@gmail.com";
    $password = $request->first_name . Str::random(6);

    $employee = new Employee();
    $employee->first_name = $request->first_name;
    $employee->last_name = $request->last_name;
    $employee->phone = $request->phone;
    $employee->address = $request->address;
    $employee->salary = $request->salary;
    $employee->year = $request->year;
    $employee->email = $email;
    $employee->password = Hash::make($password);
    $employee->type = $request->type;

    $employee->save();
    return response()->json([$employee->email, $password]);

}

public function delete_student($student_id)
{
    $student = Student::find($student_id);
    if(!$student)
    return response()->json(['the student not found']);
    $student = $student->user;

    if($student->status == 0)
    {
        return response()->json(['the account was stopped before now']);
    }
    $student->update([
        'status' => 0,
    ]);

    return response()->json(['the account is stopped']);
    }

public function delete_parentt($parent_id)
{
    $parent = Parentt::find($parent_id);
    if(!$parent)
    {
        return response()->json(['the parent not found']);
    }
    if($parent->status == 0){
    return response()->json(['the account was stopped']);
    }
    $parent->update([
        'status' => 0,
    ]);
    return response()->json(['the account is stopped']);
}

public function delete_teacher($teacher_id)
{
    $teacher = Teacher::find($teacher_id);
    if(!$teacher)
    return response()->json(['the teacher not found']);
    $teacher = $teacher->user;

    if($teacher->status == 0)
    {
        return response()->json(['the account was stopped before now']);
    }
    $teacher->update([
        'status' => 0,
    ]);

    return response()->json(['the account is stopped']);
    }

    public function get_profile_user(){

        return $this->sendResponse2(auth()->user(),'this is user profile');

    }


    public function registerPost(Request $request, $order_id)
    {
        $order = Order::where('id', $order_id)->first();

        $user = new User();

        $user->first_name = $order->first_name;
        $user->last_name = $order->last_name;
        $user->father_name = $order->father_name;
        $user->mother_name = $request->mother_name;
        $user->birthday = $order->birthday;
        $user->gender = $order->gender;
        $user->phone = $order->phone;
        $user->address = $order->address;
        $user->year = $order->year;
        $user->image = $request->image;
        $user->email = $order->email;
        $user->password = Hash::make($request->password);
        $user->conf_password = Hash::make($request->conf_password);
        $user->user_type = $request->user_type;

        $user->save();

        // create a token
        $token = $user->createToken("auth_token")->plainTextToken;
        /// send a response
        return $token;

    }

    //عرض طلبات التسجيل بالمعهد
    public function DisplayOrderNewStudent()
    {
        $order = DB::table('orders')->where('student_id','=',null)->where('course_id','=',null)->get();

        return $order;
    }

    //إعطاء موعد لطلب تسجيل في المعهد
    public function GiveDate(Request $request, $order_id)
    {
        $order = Order::where('id',$order_id)->first();

        $request->validate([
            "date" => "required|date_format:Y-m-d|after:today"
        ]);

        $new = new Appointment;

        $new->date =$request->date;
        $new->order_id = $order_id;

        $new->save();
    }

public function student_classification($calssification,$year)
{
    if($calssification == 1){
        $stud =Student::where('calssification' ,'=', 1)->where('year',$year)
        ->get();
        foreach($stud as $stud)
        {
            echo $stud->user->first_name ."  " .$stud->user->last_name;
            echo $stud->classs->name;
            echo $stud->section->num_section;
        }

    }
    else {
        $stud =Student::where('calssification' ,'=', 0)->where('year',$year)->get();
        foreach($stud as $stud)
        {
            echo $stud->user->first_name ."  " .$stud->user->last_name;
            echo $stud->classs->name;
            echo $stud->section->num_section;
        }
    }
}

public function disply_all_student_here($year)
{
    $user = User::where('year', '=' ,$year)->get()->all();
    if($user->user_type == 'student')
    {
        return response()->json([$user]);

    }
}

public function addTeacherSchedule(Request $request,$teacher_id)
{
    $teacher = Teacher::find($teacher_id);
    if(!$teacher)
    {
        return response()->json(['the teacher not found']);
    }

    // التحقق من صحة المدخلات
    $validator = Validator::make($request->all(), [
        // 'teacher_id' => 'required|exists:teachers,id',
        'schedules' => 'required|array',
        'schedules.*.day_of_week' => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday',
        'schedules.*.start_time' => 'required|date_format:H:i',
        'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // إذا كان التحقق ناجحًا، تابع عملية إضافة الجدول
    foreach ($request->schedules as $schedule) {
        Teacher_Schedule::create([
            'teacher_id' => $teacher_id,
            'day_of_week' => $schedule['day_of_week'],
            'start_time' => $schedule['start_time'],
            'end_time' => $schedule['end_time'],
        ]);
    }

    return response()->json(['message' => 'Schedule added successfully'], 200);
}


public function updateWeeklySchedule(Request $request, $teacher_id)
    {
        $teacher = Teacher::find($teacher_id);
        if(!$teacher)
        {
            return response()->json(['the teacher not found']);
        }

        // تحقق من صحة البيانات المرسلة
        $validator = Validator::make($request->all(), [
            // 'teacher_id' => 'required|exists:teachers,id',
            'schedules' => 'required|array',
            'schedules.*.day_of_week' => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
        ]);

        // حذف برنامج الدوام السابق
        Teacher_Schedule::where('teacher_id', $teacher_id)->delete();

        // إضافة البرنامج الدوامي الجديد
        foreach ($request->schedules as $scheduleData) {
            Teacher_Schedule::create([
                'teacher_id' => $teacher_id,
                'day_of_week' => $scheduleData['day_of_week'],
                'start_time' => $scheduleData['start_time'],
                'end_time' => $scheduleData['end_time'],
            ]);
        }

        // إرجاع رسالة ناجحة
        return response()->json(['message' => 'Teacher weekly schedule updated successfully'], 200);
}

public function addAbsenceForTeacherandemployee(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'num_hour_out' => 'required|integer',
            'teacher_id' => 'nullable|exists:teachers,id',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // التأكد من أن أحدهما فقط تم تقديمه
        if (!$request->hasAny(['teacher_id', 'employee_id'])) {
            return response()->json(['message' => 'Either teacher_id or employee_id must be provided'], 422);
        }

        // إنشاء سجل الغياب
        $absence = new Out_Of_Work_Employee();
        $absence->date = $request->date;
        $absence->num_hour_out = $request->num_hour_out;
        $absence->teacher_id = $request->teacher_id;
        $absence->employee_id = $request->employee_id;
        $absence->save();

        return response()->json(['message' => 'Absence added successfully'], 200);
    }






public function desplay_teacher_salary($teacher_id , $year , $month)
    {
    $teacher = Teacher::where('id' , $teacher_id)->get()->first();
    if(!$teacher)
    {
        return response()->json(['teacher not found ']);
    }
    $salary = $this->getteacherworkhour($teacher_id , $year , $month) * $teacher->cost_hour;

    return response()->json([$teacher,$salary,'successsss']);
}

private function getteacherworkhour($teacher_id, $year, $month)
    {
        // استرجاع برنامج الدوام الأسبوعي الثابت للمعلم
        $teacherSchedule = Teacher_Schedule::where('teacher_id', $teacher_id)->get();

        // استرجاع قائمة الأيام العطل في الشهر (يمكن تركها فارغة في حال لم يكن لديك بيانات)
        $holidays = Out_Of_Work_Employee::where('teacher_id', $teacher_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->pluck('date');

        // حساب عدد الأيام في الشهر
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // تهيئة مصفوفة لتخزين تفاصيل سجل الدوام لكل يوم في الشهر
        $attendanceDetails = [];

        // تحديث تفاصيل سجل الدوام لكل يوم في الشهر
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            $dayOfWeek = $date->format('l');

            // تحقق مما إذا كان اليوم هو يوم عمل للمعلم وليس عطلة
            $isHoliday = $holidays->contains($date->format('Y-m-d'));
            $isWeekend = in_array($date->format('l'), ['Friday', 'Saturday']);
            $schedule = $teacherSchedule->firstWhere('day_of_week', $dayOfWeek);

            if ($schedule && !$isHoliday && !$isWeekend) {
                // حساب عدد ساعات العمل بين وقت البداية ووقت النهاية
                $startTime = Carbon::createFromFormat('H:i:s', $schedule->start_time);
                $endTime = Carbon::createFromFormat('H:i:s', $schedule->end_time);
                $workingHours = $endTime->diffInHours($startTime);

                $attendanceDetails[] = [// صيغة التاريخ
                    'working_hours' => $workingHours,
                ];
            } else {
                $attendanceDetails[] = [// صيغة التاريخ
                    'working_hours' => 0, // لا يوجد ساعات عمل في أيام العطل أو نهاية الأسبوع
                ];
            }
        }

        $work_hour = 0;
        for($day = 1; $day <= $daysInMonth; $day++){
            $work_hour = $work_hour+  $attendanceDetails[$day];
        }

        return response()->json([
            'attendance_details' => $attendanceDetails,
        ]);


    }

public function desplay_teacher_course($teacher_id)
{
    $teacher = Teacher::find($teacher_id);
    if(!$teacher)
    {
        return response()->json(['teacher not found ']);
    }

    return  $teacher->course;

    }


public function desplay_employee()
{
    $employee = Employee::get();
    if(!$employee)
    {
        return response()->json(['you havenot any employee']);
    }
    return response()->json([$employee,'all employee']);

    }
public function desplay_one_employee($employee_id)
{
    $employee = Employee::find($employee_id);
    if(!$employee)
    {
        return response()->json(['you havenot any employee']);
    }
    return response()->json([$employee]);

    }

public function update_employee_profile(Request $request,$employee_id)
{
    $employee = Employee::find($employee_id);
    if(!$employee)
    {
        return response()->json(['you have not any employee']);
    }
    $validator = Validator::make($request->all(),[
        'salary' => 'required',
        'type' => 'required',
        'year' => 'required',
    ]);
    if ($validator->fails()) {
        return $this->responseError(['errors' => $validator->errors()]);
    }

    $employee ->update([
        'salary' => $request->salary,
        'type' => $request->type,
        'year' => $request->year,
    ]);

    }

public function getEmployeeAttendance($employee_id, $year, $month)
{
    // انشاء تواريخ البداية والنهاية للشهر المحدد
    $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();

    // احتساب عدد الأيام الكاملة
    $totalWorkDays = $startDate->diffInDaysFiltered(function (Carbon $date) {
        return !$date->isWeekend();
    }, $endDate);

    // عدد الأيام في الشهر
    $daysInMonth = $startDate->daysInMonth;

    return response()->json([
        'employee_id' => $employee_id,
        'year' => $year,
        'month' => $month,
        'attendance_days' => $daysInMonth, // لأن الموظف يعمل اليوم كاملا
        'total_work_days' => $totalWorkDays
    ]);
    }


public function getempoyeesalary($employee_id)
{
    $employee = Employee::find($employee_id);
    if(!$employee)
    {
        return response()->json(['the employee not found']);
    }

    return $employee->salary;
}



public function add_absence_for_employee($request)
{
    $validator = Validator::make($request->all(), [
        'date' => 'required|date',
        'num_hour_out' => 'required|integer',
        'teacher_id' => 'nullable|exists:teachers,id',
        'employee_id' => 'nullable|exists:employees,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // التأكد من أن أحدهما فقط تم تقديمه
    if (!$request->hasAny(['teacher_id', 'employee_id'])) {
        return response()->json(['message' => 'Either teacher_id or employee_id must be provided'], 422);
    }


}




    public function getTeacherWorkSchedule($teacher_id, $year, $month)
{


    // استرجاع سجل غياب المدرس خلال الشهر المحدد
    $absences = Out_Of_Work_Employee::where('teacher_id', $teacher_id)
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->pluck('date')
        ->toArray();

    // تنسيق البيانات لعرضها بشكل مفهوم
    $workSchedule = [];
    foreach ($absences as $day) {
        $workSchedule[] = [
            'date' => $day,
            'hours' => 0, // عدد الساعات للأيام التي تم فيها الغياب هو صفر
        ];
    }

    // حساب عدد الأيام في الشهر
    $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

    // إضافة أيام العمل التي لم يتم فيها الغياب مع عدد ساعات العمل
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = Carbon::create($year, $month, $day);
        if ($date->dayOfWeek != Carbon::FRIDAY && $date->dayOfWeek != Carbon::SATURDAY) {
            $workHours = $this->getWorkingHoursForDay($teacher_id, $date);
            if ($workHours > 0 && !in_array($date->format('Y-m-d'), $absences)) {
                $workSchedule[] = [
                    'date' => $date->format('Y-m-d'),
                    'hours' => $workHours,
                ];
            }
        }
    }

    return response()->json([
        'teacher_id' => $teacher_id,
        'year' => $year,
        'month' => $month,
        'work_schedule' => $workSchedule,
    ]);
}

    // دالة لاستخراج عدد ساعات العمل ليوم معين
    private function getWorkingHoursForDay($teacher_id, $dayOfWeek)
{
    // استرجاع بيانات الدوام لهذا اليوم
    $workSchedule = Teacher_Schedule::where('teacher_id', $teacher_id)
        ->where('day_of_week', $dayOfWeek)
        ->first();

    if ($workSchedule) {
        // التحقق من وجود بيانات البداية والنهاية
        if (!empty($workSchedule->start_time) && !empty($workSchedule->end_time)) {
            // حساب عدد ساعات العمل بين وقت البداية ووقت النهاية
            $startTime = Carbon::createFromFormat('H:i:s', $workSchedule->start_time);
            $endTime = Carbon::createFromFormat('H:i:s', $workSchedule->end_time);
            $workingHours = $endTime->diffInHours($startTime);
            return $workingHours;
        } else {
            return 0; // إذا كانت القيم غير متوفرة
        }
    }

    return 0; // إذا لم يتم العثور على برنامج دوام لهذا اليوم
}



    public function calculatemonthlyattendance($teacher_id, $year, $month)
{
    // استرجاع برنامج الدوام الأسبوعي الثابت للمعلم
    $teacherSchedule = Teacher_Schedule::where('teacher_id', $teacher_id)->get();

    // استرجاع قائمة الأيام العطل في الشهر
    $holidays = Out_Of_Work_Employee::where('teacher_id', $teacher_id)
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->pluck('date');

    // حساب عدد الأيام في الشهر
    $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;


    // return response()->json([$daysInMonth]);
    // حساب عدد أيام الدوام وعدد ساعات الدوام لكل يوم في الشهر
    $totalWorkingDays = 0;
    $totalWorkingHours = 0;

    foreach ($teacherSchedule as $schedule) {

        $workingHours = $this->getWorkingHoursForDay($teacher_id, $schedule->day_of_week); // استرجاع عدد ساعات العمل لهذا اليوم

        $workingDaysInMonth = $this->calculateWorkingDaysInMonth($year, $month, $schedule->day_of_week, $holidays, $daysInMonth);
        $totalWorkingDays += $workingDaysInMonth;
        $totalWorkingHours += $workingDaysInMonth * $workingHours;
    }

    return response()->json([
        'teacher_id' => $teacher_id,
        'year' => $year,
        'month' => $month,
        'total_working_days' => $totalWorkingDays,
        'total_working_hours' => $totalWorkingHours,
    ]);
}
    private function calculateWorkingDaysInMonth($year, $month, $dayOfWeek, $holidays, $daysInMonth)
    {
        $totalWorkingDays = 0;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            if ($date->format('l') == $dayOfWeek && !$holidays->contains($date->format('Y-m-d')) && !in_array($date->format('l'), ['Friday', 'Saturday'])) {
                $totalWorkingDays++;
            }
        }
        return $totalWorkingDays;
    }



    public function getteacherabsences($teacher_id, $year, $month)
    {
        // استرجاع الأيام التي تغيب فيها الاستاذ خلال الشهر المحدد
        $absences = Out_Of_Work_Employee::where('teacher_id', $teacher_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereNotIn(DB::raw('DAYOFWEEK(date)'), [6, 7]) // استبعاد أيام الجمعة (6) والسبت (7)
            ->get();

        // حساب عدد الأيام التي تم غياب الاستاذ فيها وعدد الساعات التي غاب فيها
        $totalAbsenceDays = $absences->count();
        $totalAbsenceHours = $absences->sum('num_hour_out');

        return response()->json([
            'teacher_id' => $teacher_id,
            'year' => $year,
            'month' => $month,
            'total_absence_days' => $totalAbsenceDays,
            'total_absence_hours' => $totalAbsenceHours,
        ]);
    }


public function generateMonthlyAttendanceReportReport($teacher_id, $year, $month)
    {
        // استرجاع برنامج الدوام الأسبوعي الثابت للمعلم
        $teacherSchedule = Teacher_Schedule::where('teacher_id', $teacher_id)->get();

        // استرجاع قائمة الأيام العطل في الشهر (يمكن تركها فارغة في حال لم يكن لديك بيانات)
        $holidays = Out_Of_Work_Employee::where('teacher_id', $teacher_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->pluck('date');

        // حساب عدد الأيام في الشهر
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // تهيئة مصفوفة لتخزين تفاصيل سجل الدوام لكل يوم في الشهر
        $attendanceDetails = [];

        // تحديث تفاصيل سجل الدوام لكل يوم في الشهر
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            $dayOfWeek = $date->format('l');

            // تحقق مما إذا كان اليوم هو يوم عمل للمعلم وليس عطلة
            $isHoliday = $holidays->contains($date->format('Y-m-d'));
            $isWeekend = in_array($date->format('l'), ['Friday', 'Saturday']);
            $schedule = $teacherSchedule->firstWhere('day_of_week', $dayOfWeek);

            if ($schedule && !$isHoliday && !$isWeekend) {
                // حساب عدد ساعات العمل بين وقت البداية ووقت النهاية
                $startTime = Carbon::createFromFormat('H:i:s', $schedule->start_time);
                $endTime = Carbon::createFromFormat('H:i:s', $schedule->end_time);
                $workingHours = $endTime->diffInHours($startTime);

                $attendanceDetails[] = [
                    'date' => $date->format('l d-m-Y'),  // صيغة التاريخ
                    'working_hours' => $workingHours,
                ];
            } else {
                $attendanceDetails[] = [
                    'date' => $date->format('l d-m-Y'),  // صيغة التاريخ
                    'working_hours' => 0, // لا يوجد ساعات عمل في أيام العطل أو نهاية الأسبوع
                ];
            }
        }

        // ترتيب الأيام بترتيب تصاعدي
        usort($attendanceDetails, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return response()->json([
            'teacher_id' => $teacher_id,
            'year' => $year,
            'month' => $month,
            'attendance_details' => $attendanceDetails,
        ]);
    }







public function desplay_section_for_classs($class_id,$year)
{
    $classs = Classs::find($class_id);
    if(!$classs)
    {
        return response()->json(['you havenot any class']);
    }
    return  $classs->section;
}



public function desplay_all_student_regester($year)
{
    $student = User::where('year',$year)->where('user_type', 'student')->with('student')->get();
    return response()->json([$student,'all student regester here']);
}

public function desplay_classs_and_section()
{

    $classs = Classs::get()->all();
    if(!$classs)
    {
        return response()->json(['you havenot any class']);
    }
    $classs1 =  $classs->section;
    return response()->json([$classs,$classs1,'successsssssss']);
}

public function show_profile_student($student_id)
    {
        $student = Student::with('user')->find($student_id);
        if(!$student)
        {
            return response()->json('the student not found ');
        }

        return response()->json(['student' => $student, 'message' => 'Success']);
    }


public function update_profile_student(Request $request,$student_id)
    {
        $student = Student::find($student_id);
        if(!$student)
        {
            return response()->json('the student not found ');
        }
        $validator = Validator::make($request->all(),[
            'first_name' => 'required',
            'last_name' => 'required|string',
            'father_name' => 'required|string',
            'mother_name' => 'required|string',
            'phone' => 'required',
            'address' => 'required',
            'calssification' => 'required',
            'school_tuition'=>'required',
            'class_id'=>'required',
            'section_id'=>'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $user = $student->user;

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->father_name = $request->father_name;
        $user->mother_name = $request->mother_name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user_id = $user->id;
        $student->calssification = $request->calssification;
        $student->school_tuition = $request->school_tuition;
        $student->user_id = $user_id;
        $student->class_id =$request->class_id;
        $student->section_id= $request->section_id;


        $student->update();
        $user->update();
        return response()->json(['sucussssss']);

    }

public function generateMonthlyAttendanceReport($student_id, $year, $month)
    {
        // استرجاع قائمة الأيام العطل في الشهر
        $holidays = collect([]);

        // حساب عدد الأيام في الشهر
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // تهيئة مصفوفة لتخزين تفاصيل الحضور لكل يوم في الشهر
        $attendanceDetails = [];

        // تحديث تفاصيل الحضور لكل يوم في الشهر
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            $attendanceStatus = 'حاضر';

            if ($date->format('l') !== 'Friday' && $date->format('l') !== 'Saturday') {
                $absence = Out_Of_Work_Student::where('student_id', $student_id)
                    ->whereDate('date', $date)
                    ->first();

                if ($absence) {
                    $attendanceStatus = 'غائب';
                }
            } else {
                $attendanceStatus = 'عطلة';
            }

            $attendanceDetails[] = [
                'date' => $date->toDateString(),
                'attendance_status' => $attendanceStatus,
            ];
        }

        return response()->json([
            'student_id' => $student_id,
            'year' => $year,
            'month' => $month,
            'attendance_details' => $attendanceDetails,
        ]);
    }


public function desplay_student_marks($student_id)
    {
        // $student = Student::find($student_id);
        // if(!$student)
        // {
        //     return response()->json(['student not found ']);
        // }
        // $student->mark;
        // return response()->json([$student,'sucssssss']);
        $student = Student::where('id', $student_id)->with('mark.subject')->first();
        return $student;
    }

public function desplay_student_nots($student_id)
    {
    
        $note = Note_Student::where('student_id',$student_id)->with('user')->get();
        return $note;
    }


public function create_note_student(Request $request , $student_id)
{
    $student = Student::find($student_id);
    if(!$student)
    {
        return response()->json(['the student not found']);
    }
    $validator = Validator::make($request->all(),[
        'text'=>'required|string',
        'type'=>'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $note_student = new Note_Student();

        $note_student->type = $request->type;
        $note_student->text = $request->text;
        $note_student->student_id = $student_id;
        $note_student->user_id = auth()->user()->id;

        $note_student->save();

        return response()->json(['successssss']);

}

public function addAbsence(Request $request, $student_id)
{
    // التحقق من وجود الطالب
    $student = Student::find($student_id);
    if (!$student) {
        return response()->json(['message' => 'Student not found'], 404);
    }

    // التحقق من صحة البيانات المدخلة
    $validator = Validator::make($request->all(), [
        'date' => 'required|date',
        'justification' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // إنشاء سجل الغياب
    $absence = new Out_Of_Work_Student();
    $absence->date = $request->date;
    $absence->justification = $request->justification;
    $absence->student_id = $student_id;
    $absence->save();

    return response()->json(['message' => 'Absence added successfully'], 200);
}


public function deleteAbsence($student_id, $absence_id)
{
    $student = Student::find($student_id);
    if (!$student) {
        return response()->json(['message' => 'Student not found'], 404);
    }

    $absence = Out_Of_Work_Student::where('student_id', $student_id)->find($absence_id);
    if (!$absence) {
        return response()->json(['message' => 'Absence record not found'], 404);
    }

    // حذف سجل الغياب
    $absence->delete();

    return response()->json(['message' => 'Absence record deleted successfully'], 200);
}


public function add_mark_to_student(Request $request, $student_id)
{
    // القيام بالتحقق من وجود الطالب
    $student = Student::find($student_id);
    if (!$student) {
        return response()->json(['error' => 'The student not found'], 404);
    }

    // التحقق من صحة البيانات المدخلة
    $validator = Validator::make($request->all(), [
        'subject_id' => 'required|integer',
        'ponus' => 'nullable|numeric',
        'homework' => 'nullable|numeric',
        'oral' => 'nullable|numeric',
        'test1' => 'nullable|numeric',
        'test2' => 'nullable|numeric',
        'exam_med' => 'nullable|numeric',
        'exam_final' => 'nullable|numeric',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // إنشاء وحفظ العلامات
    $mark = new Mark;
    $mark->student_id = $student_id;
    $mark->subject_id = $request->input('subject_id');
    $mark->ponus = $request->input('ponus');
    $mark->homework = $request->input('homework');
    $mark->oral = $request->input('oral');
    $mark->test1 = $request->input('test1');
    $mark->test2 = $request->input('test2');
    $mark->exam_med = $request->input('exam_med');
    $mark->exam_final = $request->input('exam_final');

    // حساب المجموع
    $aggregate = ($mark->ponus ?? 0) + ($mark->homework ?? 0) + ($mark->oral ?? 0) + ($mark->test1 ?? 0) + ($mark->test2 ?? 0) + ($mark->exam_med ?? 0) + ($mark->exam_final ?? 0);

    // تحديد حالة الطالب (ناجح/راسب)
    $mark->state = ($aggregate > 50) ? 1 : 0;

    $mark->save();

    return response()->json(['success' => 'Marks added successfully']);
}

public function editMark(request $request,$student_id, $subject_id)
{
    $student = Student::find($student_id);
    if(!$student)
    {
        return response()->json(['error' => 'The student not found']);
    }

    // التحقق مما إذا كانت العلامة موجودة بالفعل للطالب لنفس المادة
    $mark = Mark::where('student_id', $student_id)
                ->where('subject_id', $subject_id)
                ->first();

    if(!$mark)
    {
        return response()->json(['error' => 'The mark does not exist for this student and subject']);
    }

    // التحقق من أن العلامة التي تم تعديلها تنتمي لنفس الطالب ونفس المادة
    if($mark->student_id != $student_id || $mark->subject_id != $subject_id)
    {
        return response()->json(['error' => 'The mark does not belong to the same student or subject']);
    }

    $mark->ponus = $request->ponus ?? $mark->ponus;
    $mark->homework = $request->homework ?? $mark->homework;
    $mark->oral = $request->oral ?? $mark->oral;
    $mark->test1 = $request->test1 ?? $mark->test1;
    $mark->test2 = $request->test2 ?? $mark->test2;
    $mark->exam_med = $request->exam_med ?? $mark->exam_med;
    $mark->exam_final = $request->exam_final ?? $mark->exam_final;

    $aggregate = ($mark->ponus + $mark->homework + $mark->oral +
        $mark->test1 + $mark->test2 + $mark->exam_med + $mark->exam_final);
    $mark->state = ($aggregate > 50) ? 1 : 0;

    $mark->save();

    return response()->json(['success' => 'Mark updated successfully']);
}

public function all_teatcher()
    {
        $teatcher = Teacher::with('user')->get();
        return $teatcher;
    }


// public function desplay_publish()
// {
//     $publish = Publish::get()->all();
//     return response()->json([$publish,'this is all publish']);
// }

public function display_order_for_course($course_id)
{
    $course = Course::find($course_id);
    if(!$course)
    {
        return response()->json(['course not found']);
    }
    $course->order;
    return $course;
}

public function display_details_for_course($course_id)
{
    $course = Course::find($course_id);
    if(!$course)
    {
        return response()->json(['the course not found']);
    }



    return response()->json([$course]);

}
public function display_teacher_in_course($course_id)
{
    $course = Course::find($course_id);
    if(!$course)
    {
        return response()->json(['the course not found']);
    }
    $teacher = $course->teacher;
    // return $teacher;
    $teacher1 = $teacher->user;
    foreach($teacher1 as $teachers){
    echo $teacher1->first_name ." " .$teacher1->last_name;
    }
}

public function display_subject_in_course($course_id)
{
    $course = Course::find($course_id);
    if(!$course)
    {
        return response()->json(['the course not found']);
    }
    $subject = $course->subject;
    foreach($subject as $subject)
    {
        echo $subject->name;
    }
}

// public function add_publish(Request $request)
// {
//     $validator = Validator::make($request->all(),[
//         'description'=>'required|string',
//         'course_id'=>'required',
//         ]);

//         if ($validator->fails()) {
//             return response()->json(['errors' => $validator->errors()]);
//         }

//         $publish = new Publish();
//         $publish->description = $request->description;
//         $publish->course_id = $request->course_id;
//         $publish->save();
//         return response()->json(['sucssscceccs']);
// }
public function add_publish(Request $request)
{
    $validator = Validator::make($request->all(),[
        'description'=>'required|string',
        //'course_id'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $publish = new Publish();
        $publish->description = $request->description;
        //$publish->course_id = $request->course_id ?? null;
        $publish->save();

        if ($request->path) {
            $validator = Validator::make($request->all(),[
                'path' => 'required|mimes:png,jpg,jpeg,gif,pdf,docx,txt'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Please fix the errors',
                    'errors' => $validator->errors()
                ]);
            }

            $img = $request->path;
            $ext = $img->getClientOriginalExtension();
            $imageName = time().'.'.$ext;
            $img->move(public_path().'/upload',$imageName);

            $image = new Image;
            $image->path = $imageName;
            $image->description = $request->description;
            $image->publish_id = $publish->id;

            $image->save();

            return response()->json([
                'status' => 'true',
                'message' => 'image upload success',
                'path' => asset('/upload/'.$imageName),
                'data' => $image
            ]);
            return response()->json(['sucssscceccs with img']);
        }

        else {
            return response()->json(['sucssscceccs']);
        }
    }




// public function delete_publish($publish_id)
// {
//     $publish = Publish::find($publish_id);
//     if(!$publish)
//     {
//         return response()->json(['the publish not found or was deleted  ']);
//     }
//     $publish->delete();
//     return response()->json(['the publish  deleted  ']);

// }
public function delete_publish($publish_id)
{
    $publish = Publish::find($publish_id);
    if(!$publish)
    {
        return response()->json(['the publish not found or was deleted  ']);
    }
    $publish->delete();
    $image = Image::where('publish_id', $publish->id)->delete();
    return response()->json(['the publish  deleted  ']);

}

// public function update_publish(Request $request,$publish_id)
// {
//     $publish = Publish::find($publish_id);
//     if(!$publish)
//     {
//         return response()->json(['the publish not found']);
//     }
//     $validator = Validator::make($request->all(),[
//         'description'=>'required|string',
//         //'course_id'=>'required',
//         ]);

//         if ($validator->fails()) {
//             return response()->json(['errors' => $validator->errors()]);
//         }

//         if ($request->has('description')) {
//             $publish->description = $request->description;
//         }

//         $image = Image::where('publish_id', $publish_id)->first();

//         if (!$image) {
//             $validator = Validator::make($request->all(),[
//                 'path' => 'required|mimes:png,jpg,jpeg,gif,pdf,docx,txt'
//             ]);

//             if ($validator->fails()) {
//                 return response()->json([
//                     'status' => 'false',
//                     'message' => 'Please fix the errors',
//                     'errors' => $validator->errors()
//                 ]);
//             }

//             $img = $request->path;
//             $ext = $img->getClientOriginalExtension();
//             $imageName = time().'.'.$ext;
//             $img->move(public_path().'/upload',$imageName);

//             $image = new Image;
//             $image->path = $imageName;
//             $image->publish_id = $publish_id;
//             $image->description = $request->description ?? $publish->description;
//             $image->save();

//             return response()->json([
//                 'status' => 'true',
//                 'message' => 'image upload success',
//                 'path' => asset('/upload/'.$imageName),
//                 'data' => $image
//             ]);
//         }

//         // حذف الصورة القديمة من المجلد إذا كانت موجودة
//         $oldImagePath = public_path().'/upload/'.$image->path;
//         if (file_exists($oldImagePath)) {
//             unlink($oldImagePath);
//         }

//         // رفع الصورة الجديدة
//         $img = $request->path;
//         $ext = $img->getClientOriginalExtension();
//         $imageName = time().'.'.$ext;
//         $img->move(public_path().'/upload', $imageName);

//         // تحديث مسار الصورة في قاعدة البيانات
//         $image->path = $imageName;

//         if ($request->has('description')) {
//             $image->description = $request->description;
//         }
//         $image->save();

//         return response()->json([
//             'status' => 'true',
//             'message' => 'Image updated successfully',
//             'path' => asset('/upload/'.$imageName),
//             'data' => $image
//         ]);


//         // $publish->course_id = $request->course_id;
//         $publish->save();
//         return response()->json(['sucssscceccs']);
// }

public function update_publish(Request $request, $publish_id)
{
    // البحث عن الكائن المحدد
    $publish = Publish::find($publish_id);
    if (!$publish) {
        return response()->json(['message' => 'The publish not found']);
    }

    // التحقق من صحة المدخلات
    $validator = Validator::make($request->all(), [
        'description' => 'nullable|string',
        //'course_id' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()]);
    }

    // تحديث الوصف في الكائن Publish إذا كان موجودًا في الطلب
    if ($request->has('description')) {
        $publish->description = $request->description;
    }

    // البحث عن الصورة المرتبطة بالكائن
    $image = Image::where('publish_id', $publish_id)->first();

    if ($request->hasFile('path')) {
        // التحقق من صحة الصورة المرفوعة
        $validator = Validator::make($request->all(), [
            'path' => 'required|mimes:png,jpg,jpeg,gif,pdf,docx,txt'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Please fix the errors',
                'errors' => $validator->errors()
            ]);
        }

        if ($image) {
            // حذف الصورة القديمة من المجلد إذا كانت موجودة
            $oldImagePath = public_path() . '/upload/' . $image->path;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        } else {
            // إنشاء كائن جديد للصورة إذا لم يكن موجودًا
            $image = new Image;
            $image->publish_id = $publish_id;
        }

        // رفع الصورة الجديدة
        $img = $request->file('path');
        $ext = $img->getClientOriginalExtension();
        $imageName = time() . '.' . $ext;
        $img->move(public_path('/upload'), $imageName);

        // تحديث مسار الصورة في قاعدة البيانات
        $image->path = $imageName;
    }

    // تحديث الوصف في كائن Image إذا كان موجودًا في الطلب
    if ($request->has('description')) {
        $image->description = $request->description;
    }

    // حفظ التغييرات في كائن Image
    if ($image) {
        $image->save();
    }

    // حفظ التحديثات في الكائن Publish
    $publish->save();

    return response()->json([
        'status' => 'true',
        'message' => 'Publish and image updated successfully',
        'publish' => $publish,
        'image' => $image,
        'path' => isset($imageName) ? asset('/upload/' . $imageName) : null
    ]);
}




public function add_to_expensess(Request $request)
{
    $validator = Validator::make($request->all(),[
        'date' => 'required|date',
        'product'=>'required',
        'cost_one_piece'=>'required',
        'num_product'=>'required',
        'year'=>'required'
    ]);

    if ($validator->fails()) {
        return $this->responseError(['errors' => $validator->errors()]);
    }

    $expenses = new Expenses();
    $expenses->date = $request->date;
    $expenses->product = $request->product;
    $expenses->cost_one_piece = $request->cost_one_piece;
    $expenses->num_product = $request->num_product;
    $expenses->total_cost = $request->num_product * $request->cost_one_piece;
    $expenses->year = $request->year;
    $expenses->save();
    return response()->json(['sucsseesss']);

}


public function add_to_break(Request $request)
{
    $validator = validator::make($request->all(),[
        'first_name'=>'required',
        'last_name'=>'required',
        'phone'=>'required',
        'address'=>'required',
        'year'=>'required',
        'cost_from_breake'=>'required',
    ]);

    if ($validator->fails()) {
        return $this->responseError(['errors' => $validator->errors()]);
    }

    $break = new Breake();
    $break->first_name = $request->first_name;
    $break->last_name = $request->last_name;
    $break->phone = $request->phone;
    $break->address = $request->address;
    $break->year = $request->year;
    $break->cost_from_breake = $request->cost_from_breake;

    $break->save();

}



// public function add_mark_to_student(request $request,$student_id)
// {
//     $student = Student::find($student_id);
//     if(!$student)
//     {
//         return response()->json(['the student not found']);
//     }

//     $mark = new Mark;
//     $mark->ponus = $request->ponus  ;
//     $mark->homework = $request->homework || null;
//     $mark->oral = $request->oral || null;
//     $mark->test1 = $request->test1 || null;
//     $mark->test2 = $request->test2 || null;
//     $mark->exam_med = $request->exam_med || null;
//     $mark->exam_final = $request->exam_final || null;
//     $aggregrate = ($request->ponus + $request->homework
//     + $request->oral + $request->test1
//     +$request->test2 + $request->exam_med
//     +$request->exam_final);
//     if ($aggregrate > 50){
//     $mark->state = 1;
//     }
//     else {
//         $mark->state = 0;
//     }
//     $mark->student_id = $student_id;
//     $mark->subject_id = $request->subject_id;
//     $mark->save();

//     return response()->json(['succusssss']);

// }


public function calculateMonthlySalary($teacher_id, $year, $month)
{
    // استرجاع برنامج الدوام الأسبوعي للأستاذ
    $teacherSchedule = Teacher_Schedule::where('teacher_id', $teacher_id)->get();

    // استرجاع قائمة أيام العطل والغيابات في الشهر
    $holidays = Out_Of_Work_Employee::where('teacher_id', $teacher_id)
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->pluck('date')->toArray();

    // حساب عدد الأيام في الشهر
    $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

    // استرجاع أجر الساعة للأستاذ
    $teacher = Teacher::findOrFail($teacher_id);
    $hourlyRate = $teacher->hourly_rate;

    $totalWorkingHours = 0;

    // حساب عدد ساعات العمل في الشهر
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = Carbon::createFromDate($year, $month, $day);
        $dayOfWeek = $date->format('l');

        if (in_array($date->toDateString(), $holidays) || in_array($dayOfWeek, ['Friday', 'Saturday'])) {
            continue; // تخطي أيام العطل والغيابات
        }

        foreach ($teacherSchedule as $schedule) {
            if ($schedule->day_of_week == $dayOfWeek) {
                $workingHours = $this->getWorkingHoursForDays($schedule);
                $totalWorkingHours += $workingHours;
            }
        }
    }
    $teacher = Teacher::find($teacher_id);
    $hourlyRate = $teacher->cost_hour ;
    $addedHour = $teacher->num_hour_added ;
    // حساب الراتب الشهري
    $monthlySalary = ($totalWorkingHours + $addedHour) * $hourlyRate;

    return response()->json([
        'teacher_id' => $teacher_id,
        'year' => $year,
        'month' => $month,
        'total_working_hours' => $totalWorkingHours,
        'monthly_salary' => $monthlySalary,
    ]);
}

private function getWorkingHoursForDays($schedule)
{
    // حساب عدد ساعات العمل بين وقت البداية ووقت النهاية
    $startTime = Carbon::createFromFormat('H:i:s', $schedule->start_time);
    $endTime = Carbon::createFromFormat('H:i:s', $schedule->end_time);
    $workingHours = $endTime->diffInHours($startTime);
    return $workingHours;
}

//تعديل معلومات المعهد
public function edit_info_academy(Request $request,$id)
{
    $info = Academy::find($id);

    $info->name = $request->name ?? $info->name;
    $info->phone = $request->phone ?? $info->phone;
    $info->address = $request->address ?? $info->address;
    $info->facebook_link = $request->facebook_link ?? $info->facebook_link;
    $info->description = $request->description ?? $info->description;
    $info->year = $request->year ?? $info->year;

    $info->save();

    return $info;
}

//تعديل السنة الدراسية
// public function edit_year(Request $request,$id)
// {
//     // $info = Academy::find($id);

//     // $info->year = $request->year ?? $info->year;

//     // $info->name = $info->name;
//     // $info->phone = $info->phone;
//     // $info->address = $info->address;
//     // $info->facebook_link = $info->facebook_link;
//     // $info->description = $info->description;

//     // // if ($request->has('year')) {
//     // //     $info->year = $request->year ;
//     // // }
//     // $info->save();

//     // return $info;
//     $info = Academy::find($id);

//     // $info->name = $request->name ?? $info->name;
//     // $info->phone = $request->phone ?? $info->phone;
//     // $info->address = $request->address ?? $info->address;
//     // $info->facebook_link = $request->facebook_link ?? $info->facebook_link;
//     $info->year = $request->year ?? $info->year;

//     $info->save();

//     return $info;
// }

public function edit_year(Request $request,$id)
{
    $info = Academy::find($id);

    $info->year = $request->year ?? $info->year;

    $info->save();

    return $info;
    
}

public function student_course($student_id)
    {
        // $student = Student::where('user_id', auth()->user()->id)->first();
        // if (!$student) {
        //     return response()->json(['error' => 'Student not found'], 404);
        // }
        
        $order = Order::where('student_id', $student_id)->with('course.teacher.user')->get();

        return $order;
    }

//إضافة دورة مع إمكانية إضافة إعلان إن أردنا أو عدم إضافة    
// public function add_course(Request $request)
// {
//     $academy = Academy::find(1);

//     $course = new Course;

//     $course->name_course = $request->name_course;
//     $course->description = $request->description;
//     $course->cost_course = $request->cost_course;
//     $course->num_day = $request->num_day;
//     $course->start_date= $request->start_date;
//     $course->finish_date= $request->finish_date;
//     $course->start_time= $request->start_time;
//     $course->finish_time= $request->finish_time;
//     $course->percent_teacher= $request->percent_teacher;
//     $course->year= $academy->year;
//     $course->class_id= $request->class_id;

//     //تحديد المادة للدورة
//     $name_subject = $request->name_subject;//لازم يكون اسم المادة يلي بدي دخلو نفس كتابة أسماء المواد يلي بالداتا عندي
//     $subject= Subject::where('name',$name_subject)->where('class_id',$course->class_id)->first();
//     $subject_id = $subject->id;
//     $course->subject_id= $subject_id;

//     //تحديد المدرس للدورة
//     // $name_teacher = $request->name_teacher;//لازم يكون اسم الأستاذ يلي بدي دخلو نفس كتابة أسماء المستخدم الأستاذ يلي بالداتا عندي
//     // $user_teacher= User::where('first_name',$name_teacher)->where('user_type','teacher')->first();
//     // $teacher = Teacher::where('user_id', $user_teacher->id)->first();
//     // $teacher_id = $teacher->id;
//     // $course->teacher_id= $teacher_id;
//     $course->teacher_id= $request->teacher_id;

//     $course->save();


//     //إضافة إعلان للدورة ،ممكن يضيف و ممكن لا
//     // $validator = Validator::make($request->all(),[
//     //     'description'=>'required|string',
//     //     'course_id'=>'required',
//     //     ]);

//         // if ($validator->fails()) {
//         //     return response()->json(['errors' => $validator->errors()]);
//         // }

//         if ($request->description_publish) {
//             $publish = new Publish();
//         $publish->description = $request->description_publish;
//         $publish->course_id = $course->id;
//         $publish->save();

//         if ($request->path) {
//             $validator = Validator::make($request->all(),[
//                 'path' => 'required|mimes:png,jpg,jpeg,gif,pdf,docx,txt'
//             ]);

//             if ($validator->fails()) {
//                 return response()->json([
//                     'status' => 'false',
//                     'message' => 'Please fix the errors',
//                     'errors' => $validator->errors()
//                 ]);
//             }

//             $img = $request->path;
//             $ext = $img->getClientOriginalExtension();
//             $imageName = time().'.'.$ext;
//             $img->move(public_path().'/upload',$imageName);

//             $image = new Image;
//             $image->path = $imageName;
//             $image->description = $request->description_publish;
//             $image->publish_id = $publish->id;

//             $image->save();

//             return response()->json([
//                 'status' => 'true',
//                 'message' => 'course with publish text and image upload success',
//                 'course' => $course,
//                 'path' => asset('/upload/'.$imageName),
//                 'data_image' => $image
//             ]);
//             return response()->json(['sucssscceccs with img']);
//         }

//         else {
//             return response()->json(['sucssscceccs']);
//         }
//         }
        


//     return $course;
// }
// public function add_course(Request $request)
// {
//     $academy = Academy::find(1);

//     $validator = Validator::make($request->all(), [
//         'name_course' => 'required|string',
//         'description' => 'required|string',
//         'cost_course' => 'required|numeric',
//         'num_day' => 'required|integer|min:1',
//         'start_date' => 'required|date',
//         'finish_date' => 'required|date|after:start_date',
//         'start_time' => 'required|date_format:H:i',
//         'finish_time' => 'required|date_format:H:i|after:start_time',
//         'percent_teacher' => 'required|numeric|between:0,100',
//         'class_id' => 'required|exists:classses,id',
//         'teacher_id' => 'required|exists:teachers,id',
//         'name_subject' => 'required|string|exists:subjects,name',
//         'description_publish' => 'nullable|string',
//         'path' => 'nullable|mimes:png,jpg,jpeg,gif,pdf,docx,txt'
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'status' => 'false',
//             'message' => 'Please fix the errors',
//             'errors' => $validator->errors()
//         ]);
//     }

//     // التحقق من فرق الأيام بين start_date و finish_date
//     $start_date = \Carbon\Carbon::parse($request->start_date);
//     $finish_date = \Carbon\Carbon::parse($request->finish_date);
//     $diffInDays = $start_date->diffInDays($finish_date)+2;

//     if ($request->num_day > $diffInDays) {
//         return response()->json([
//             'status' => 'false',
//             'message' => 'The num_day should not be greater than the difference in days between start_date and finish_date',
//         ]);
//     }

//     $course = new Course;
//     $course->name_course = $request->name_course;
//     $course->description = $request->description;
//     $course->cost_course = $request->cost_course;
//     $course->num_day = $request->num_day;
//     $course->start_date = $request->start_date;
//     $course->finish_date = $request->finish_date;
//     $course->start_time = $request->start_time;
//     $course->finish_time = $request->finish_time;
//     $course->percent_teacher = $request->percent_teacher;
//     $course->year = $academy->year;
//     $course->class_id = $request->class_id;

//     // تحديد المادة للدورة
//     $subject = Subject::where('name', $request->name_subject)->where('class_id', $course->class_id)->first();
//     if (!$subject) {
//         return response()->json([
//             'status' => 'false',
//             'message' => 'Subject not found for the specified class_id',
//         ]);
//     }
//     $course->subject_id = $subject->id;

//     // تحديد المدرس للدورة
//     $course->teacher_id = $request->teacher_id;

//     $course->save();
// if ($request->product) {
//      //مصاريف الدورة
//      $expenses = new Expenses;

//      $expenses->date = $request->date;
//      $expenses->product = $request->product;
//      $expenses->cost_one_piece = $request->cost_one_piece;
//      $expenses->num_product = $request->num_product;
//      $expenses->total_cost = $expenses->cost_one_piece * $expenses->num_product;
//      $expenses->year = $academy->year;
//      $expenses->course_id = $course->id;
 
//      $expenses->save();
 
// }
   
//     // إضافة إعلان للدورة، يمكن أن يضيف ويمكن لا
//     if ($request->description_publish) {
//         $publish = new Publish();
//         $publish->description = $request->description_publish;
//         $publish->course_id = $course->id;
//         $publish->save();

//         if ($request->path) {
//             $img = $request->file('path');
//             $ext = $img->getClientOriginalExtension();
//             $imageName = time().'.'.$ext;
//             $img->move(public_path().'/upload', $imageName);

//             $image = new Image;
//             $image->path = $imageName;
//             $image->description = $request->description_publish;
//             $image->publish_id = $publish->id;
//             $image->save();

//             return response()->json([
//                 'status' => 'true',
//                 'message' => 'Course with publish text and image upload success',
//                 'course' => $course,
//                 'path' => asset('/upload/'.$imageName),
//                 'data_image' => $image
//             ]);
//         }

//         return response()->json([
//             'status' => 'true',
//             'message' => 'Course with publish text success',
//             'course' => $course,
//         ]);
//     }

//     return response()->json([
//         'status' => 'true',
//         'message' => 'Course created successfully',
//         'course' => $course
//     ]);
// }


public function add_course(Request $request)
{
    $academy = Academy::find(1);

    $validator = Validator::make($request->all(), [
        'name_course' => 'required|string',
        'description' => 'required|string',
        'cost_course' => 'required|numeric',
        'num_day' => 'required|integer|min:1',
        'start_date' => 'required|date',
        'finish_date' => 'required|date|after:start_date',
        'start_time' => 'required|date_format:H:i',
        'finish_time' => 'required|date_format:H:i|after:start_time',
        'percent_teacher' => 'required|numeric|between:0,100',
        'class_id' => 'required|exists:classses,id',
        'teacher_id' => 'required|exists:teachers,id',
        'name_subject' => 'required|string|exists:subjects,name',
        'description_publish' => 'nullable|string',
        'path' => 'nullable|mimes:png,jpg,jpeg,gif,pdf,docx,txt',
        'expenses.*.date' => 'required|date',
        'expenses.*.product' => 'required|string',
        'expenses.*.cost_one_piece' => 'required|numeric',
        'expenses.*.num_product' => 'required|integer|min:1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'false',
            'message' => 'Please fix the errors',
            'errors' => $validator->errors()
        ]);
    }

    // التحقق من فرق الأيام بين start_date و finish_date
    $start_date = \Carbon\Carbon::parse($request->start_date);
    $finish_date = \Carbon\Carbon::parse($request->finish_date);
    $diffInDays = $start_date->diffInDays($finish_date) + 2;

    if ($request->num_day > $diffInDays) {
        return response()->json([
            'status' => 'false',
            'message' => 'The num_day should not be greater than the difference in days between start_date and finish_date',
        ]);
    }

    $course = new Course;
    $course->name_course = $request->name_course;
    $course->description = $request->description;
    $course->cost_course = $request->cost_course;
    $course->num_day = $request->num_day;
    $course->start_date = $request->start_date;
    $course->finish_date = $request->finish_date;
    $course->start_time = $request->start_time;
    $course->finish_time = $request->finish_time;
    $course->percent_teacher = $request->percent_teacher;
    $course->year = $academy->year;
    $course->class_id = $request->class_id;

    // تحديد المادة للدورة
    $subject = Subject::where('name', $request->name_subject)->where('class_id', $course->class_id)->first();
    if (!$subject) {
        return response()->json([
            'status' => 'false',
            'message' => 'Subject not found for the specified class_id',
        ]);
    }
    $course->subject_id = $subject->id;

    // تحديد المدرس للدورة
    $course->teacher_id = $request->teacher_id;

    $course->save();

    // إضافة المصاريف المتعددة
    if ($request->has('expenses')) {
        foreach ($request->expenses as $expenseData) {
            $expenses = new Expenses;
            $expenses->date = $expenseData['date'];
            $expenses->product = $expenseData['product'];
            $expenses->cost_one_piece = $expenseData['cost_one_piece'];
            $expenses->num_product = $expenseData['num_product'];
            $expenses->total_cost = $expenses->cost_one_piece * $expenses->num_product;
            $expenses->year = $academy->year;
            $expenses->course_id = $course->id;
            $expenses->save();
        }
    }

    // إضافة إعلان للدورة، يمكن أن يضيف ويمكن لا
    if ($request->description_publish) {
        $publish = new Publish();
        $publish->description = $request->description_publish;
        $publish->course_id = $course->id;
        $publish->save();

        if ($request->path) {
            $img = $request->file('path');
            $ext = $img->getClientOriginalExtension();
            $imageName = time().'.'.$ext;
            $img->move(public_path().'/upload', $imageName);

            $image = new Image;
            $image->path = $imageName;
            $image->description = $request->description_publish;
            $image->publish_id = $publish->id;
            $image->save();

            return response()->json([
                'status' => 'true',
                'message' => 'Course with publish text and image upload success',
                'course' => $course,
                'path' => asset('/upload/'.$imageName),
                'data_image' => $image,
                'expenses' => $expenses
            ]);
        }

        return response()->json([
            'status' => 'true',
            'message' => 'Course with publish text success',
            'course' => $course,
        ]);
    }

    return response()->json([
        'status' => 'true',
        'message' => 'Course created successfully',
        'course' => $course
    ]);
}

public function update_course(Request $request, $course_id)
{

}

// public function display_info_course($course_id)
// {
//     $course = Course::where('id', $course_id)->with('publish.image')->with('expens')->first();

//     //كلشي تحت لتغير حالة الدورة من قيد الدراسة إلى مفتوحة
//         //عدد الطلاب المسجلين في الدورة
//         $num_order_for_course = Order::where('course_id',$course_id)->count();

//         // $course = Course::find($course_id);

//         //المبلغ الذي جمعه المعهد من الطلاب المسجلين
//         $Money = $num_order_for_course * $course->cost_course;

//         // المبلغ الذي جمعه المعهد بعد إعطاء المدرس نسبته
//         $Money_without_teacher = $Money * (100 - $course->percent_teacher) / 100;
//         // return (100 - $course->percent_teacher);

//         //مبلغ دفعو المعهد كرمال الدورة
//         $expenses = Expenses::where('course_id',$course_id)->sum('total_cost') ?? 0;

//         //مربح المعهد من الدورة إذا تم فتحها
//         $Money_win_ =  $Money_without_teacher - $expenses ;

//         // حساب عدد الطلاب اللازمين لفتح الدورة
//     $required_money_to_open = $expenses / ((100 - $course->percent_teacher) / 100);
//     $num_students_required = ceil($required_money_to_open / $course->cost_course);

//     // حساب عدد الطلاب المتبقي لتغطية التكاليف
//     $num_students_remaining = $num_students_required - $num_order_for_course;

//     return $num_students_required;
//         if ($Money_win >= 500000) {
//             $course->Course_status = 1;
//             $course->save();
//         }
//         else {
//             // المبلغ المتبقي لفتح الدورة ليكون المعهد أخذ ربحه الأدنى
//             $x= 500000-$Money_win;

//             $s = $x / $course->cost_course;

//             $num_order_for_course + $s 


//         }

//         // $num_student_to_open_course = 
    

//     // عدد الطلاب اللازم لحتى تنفتح الدور
//     //مجموع المصلريف و نسبة الأستاذ
//     //
//     return $Money_win;


// }


public function display_info_course($course_id)
{
    $course = Course::where('id', $course_id)
        ->with('publish.image')
        ->with('expens')
        ->first();

    if (!$course) {
        return response()->json([
            'status' => 'false',
            'message' => 'Course not found'
        ]);
    }

    // عدد الطلاب المسجلين في الدورة
    $num_order_for_course = Order::where('course_id', $course_id)->count();

    // المبلغ الذي جمعه المعهد من الطلاب المسجلين
    $Money = $num_order_for_course * $course->cost_course;

    // مصاريف الدورة الكلية
    $expenses = Expenses::where('course_id', $course_id)->sum('total_cost') ?? 0;

    // النسبة التي يحصل عليها المعهد بعد خصم نسبة المدرس
    $institute_percentage = 100 - $course->percent_teacher;

    // المبلغ الذي يجب جمعه ليغطي المصاريف ويحقق الربح المطلوب
    $required_money_to_open = $expenses + 500000;  // إضافة الربح المطلوب 500000 إلى المصاريف

    // المبلغ الذي يجب جمعه من الطلاب ليغطي المطلوب بعد خصم نسبة المدرس
    $required_total_money = $required_money_to_open / ($institute_percentage / 100);

    // حساب عدد الطلاب اللازمين لجمع هذا المبلغ
    $num_students_required = ceil($required_total_money / $course->cost_course);

    // حساب عدد الطلاب المتبقيين لتغطية التكاليف
    $num_students_remaining = $num_students_required - $num_order_for_course;

    // تغيير حالة الدورة إذا كانت الشروط مستوفاة
    if ($Money >= $required_money_to_open) {
        $course->Course_status = 1;
        $course->save();
    }

    return response()->json([
        'status' => 'true',
        'course' => $course,
        'num_students_registered_in_course' => $num_order_for_course,
        'total_money_collected' => $Money,
        'total_expenses' => $expenses,
        'num_students_required_shoud' => $num_students_required,
        'num_students_remaining' => $num_students_remaining
    ]);
}





}
