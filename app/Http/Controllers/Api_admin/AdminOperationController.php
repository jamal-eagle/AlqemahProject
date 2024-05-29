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
use App\Models\Classs;
use App\Models\Student;
use App\Models\Parentt;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Employee;
use App\Models\Mark;
use Illuminate\Support\Str;
use App\Models\Note;
use App\Models\Note_Student;
use App\Models\Publish;
use Illuminate\Support\Carbon;
use App\Models\Out_Of_Work_Employee;
use App\Models\Teacher_Schedule;

class AdminOperationController extends BaseController
{

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
            if($user){
        $request->user()->currentAccessToken()->delete();
        return $this->responseError(['the user logged out']);
            }
            else {
                $request->parentt()->currentAccessToken()->delete();
                return $this->responseError(['the user logged out']);
            }
    }
    }



    public function register_student(Request $request,$order_id)
    {

        $order = Order::find($order_id)->where('id',$order_id)->get;
        $validator = Validator::make($request->all(),[
            'user_type' => 'required|default:student',
        ]);

        if ($validator->fails()) {
            return $this->responseError(['errors' => $validator->errors()]);
        }
        $email = $order->first_name.Str::random(5)."@gmail.com";
        $password = $order->first_name.Str::random(6);
        $user = new User();

        $user->first_name = $order->first_name;
        $user->last_name = $order->last_name;
        $user->father_name = $order->father_name;
        $user->mother_name = $order->mother_name;
        $user->birthday = $order->birthday;
        $user->gender = $order->gender;
        $user->phone = $order->phone;
        $user->address = $order->address;
        $user->year = $order->year;
        $user->image = $order->image;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->conf_password = Hash::make($password);
        $user->user_type = $request->user_type;


        $user->save();

        $validator1 = Validator::make($request->all(),[
            'calssification' => 'required|string',
            'school_tuition' => 'required',
            'class_id'=> 'required',
            'section_id'=>'required',
            'parentt_id'=>'required',
        ]);

        if ($validator1->fails()) {
            return $this->responseError(['errors' => $validator1->errors()]);
        }

        $student = new Student;
        $student->calssification = $request->calssification;
        $student->school_tuition = $request->school_tuition;
        $student->user_id = $user->id;
        $student->class_id =$request->class_id;
        $student->parentt_id = $request->parentt_id;

        $student->save();


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



    }

    public function show_profile_student($student_id)
    {
        $student = Student::find($student_id);
        if(!$student)
        {
            return response()->json('the student not found ');
        }
        $student->user;
        return response()->json([$student,'sucsseesss ']);

    }

    public function update_profile_student(Request $request,$student_id)
    {
        $student = Student::find($student_id);
        if(!$student)
        {
            return response()->json('the student not found ');
        }
        $validator = Validator::make($request->all(),[
            'calssification' => 'required',
            'school_tuition'=>'required',
            'class_id'=>'required',
            'section_id'=>'required',
            'parentt_id'=>'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $user = $student->user;
        $user_id = $user->id;
        $student->calssification = $request->calssification;
        $student->school_tuition = $request->school_tuition;
        $student->user_id = $user_id;
        $student->class_id =$request->class_id;
        $student->section_id= $request->section_id;
        $student->parentt_id = $request->parentt_id;

        $student->update();
        return response()->json(['sucussssss']);

    }

    public function desplay_student_marks($student_id)
    {
        $student = Student::find($student_id);
        if(!$student)
        {
            return response()->json(['student not found ']);
        }
        $student->mark;
        return response()->json([$student,'sucssssss']);

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

    public function student_classification($classifaction)
    {
        if($classifaction = 1){
        $stud =Student::where('calssification' ,'=', 1)->with('user')->get();
       // $student = User::where($stud->user_id, 'id')->get()->all();
            return response()->json([$stud]);
    }
    else {
        $stud =Student::where('calssification' ,'=', 0)->with('user')->get();
        //$student = User::where($stud->user_id, 'id')->get()->all();
            return response()->json([$stud]);
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

public function desplay_teacher_salary($teacher_id)
{
    $teacher = Teacher::where('id' , $teacher_id)->get()->first();
    if(!$teacher)
    {
        return response()->json(['teacher not found ']);
    }
    $salary = ($teacher->num_hour * $teacher->cost_hour) + ($teacher->num_our_added * $teacher->cost_hour);

    return response()->json([$teacher,$salary,'successsss']);
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

public function addWeekly_Schedule_for_student(Request $request)
{
    // تحقق من صحة البيانات المرسلة
    $request->validate([
        'teacher_id' => 'required|exists:teachers,id',
        'schedules' => 'required|array|min:7',
        'schedules.*.day_of_week' => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
        'schedules.*.start_time' => 'required|date_format:H:i',
        'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
    ]);

    $teacher_id = $request->teacher_id;
    $schedulesData = $request->schedules;

    foreach ($schedulesData as $scheduleData) {
        Teacher_Schedule::create([
            'teacher_id' => $teacher_id,
            'day_of_week' => $scheduleData['day_of_week'],
            'start_time' => $scheduleData['start_time'],
            'end_time' => $scheduleData['end_time'],
        ]);
    }

    // إرجاع رد ناجح
    return response()->json(['message' => 'Teacher weekly schedule added successfully'], 201);
}


public function updateWeeklySchedule(Request $request, $teacher_id)
    {
        // تحقق من صحة البيانات المرسلة
        $request->validate([
            'schedules' => 'required|array|min:7',
            'schedules.*.day_of_week' => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
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


    public function getteacherworkschedule($teacher_id, $year, $month)
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

        // إضافة أيام العمل التي لم يتم فيها الغياب مع عدد ساعات العمل
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth();
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            if ($date->dayOfWeek != Carbon::FRIDAY && $date->dayOfWeek != Carbon::SATURDAY) {
                $workHours = $this->getWorkingHoursForDay($teacher_id, $date);
                if ($workHours > 0) {
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
    private function getWorkingHoursForDay($teacher_id, $date)
    {
        // استرجاع عدد ساعات العمل من جدول برنامج الدوام
        $workSchedule = Teacher_Schedule::where('teacher_id', $teacher_id)
            ->whereDate('date', $date)
            ->first();

        if ($workSchedule) {
            return $workSchedule->work_hours;
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
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth();

        // حساب عدد أيام الدوام وعدد ساعات الدوام لكل يوم في الشهر
        $totalWorkingDays = 0;
        $totalWorkingHours = 0;
        foreach ($teacherSchedule as $schedule) {
            $workingHours = $this->getWorkingHoursForDay($teacher_id,$schedule->day_of_week); // استرجاع عدد ساعات العمل لهذا اليوم
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


public function getEmployeeAttendance($employeeId, $year, $month)
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
        'employee_id' => $employeeId,
        'year' => $year,
        'month' => $month,
        'attendance_days' => $daysInMonth, // لأن الموظف يعمل اليوم كاملا
        'total_work_days' => $totalWorkDays
    ]);
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


public function desplay_section_for_classs($class_id)
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
    $student = User::where('year',$year)->with('student')->get()->all();
    return response()->json([$student,'all student regester here']);
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $note_student = new Note_Student();
        $note_student->text = $request->text;
        $note_student->student_id = $student_id;
        $note_student->user_id = auth()->user()->id;

        $note_student->save();

        return response()->json(['successssss']);

}

public function desplay_publish()
{
    $publish = Publish::get()->all();
    return response()->json([$publish,'this is all publish']);
}

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

public function add_publish(Request $request)
{
    $validator = Validator::make($request->all(),[
        'description'=>'required|string',
        'course_id'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $publish = new Publish();
        $publish->description = $request->description;
        $publish->course_id = $request->course_id;
        $publish->save();
        return response()->json(['sucssscceccs']);
}

public function delete_publish($publish_id)
{
    $publish = Publish::find($publish_id);
    if(!$publish)
    {
        return response()->json(['the publish not found or was deleted  ']);
    }
    $publish->delete();
    return response()->json(['the publish  deleted  ']);

}

public function update_publish(Request $request,$publish_id)
{
    $publish = Publish::find($publish_id);
    if(!$publish)
    {
        return response()->json(['the publish not found']);
    }
    $validator = Validator::make($request->all(),[
        'description'=>'required|string',
        'course_id'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $publish->description = $request->description;
        $publish->course_id = $request->course_id;
        $publish->update();
        return response()->json(['sucssscceccs']);
}

public function add_mark_to_student(request $request,$student_id)
{
    $student = Student::find($student_id);
    if(!$student)
    {
        return response()->json(['the student not found']);
    }

    $mark = new Mark;
    $mark->ponus = $request->ponus  ;
    $mark->homework = $request->homework || null;
    $mark->oral = $request->oral || null;
    $mark->test1 = $request->test1 || null;
    $mark->test2 = $request->test2 || null;
    $mark->exam_med = $request->exam_med || null;
    $mark->exam_final = $request->exam_final || null;
    $aggregrate = ($request->ponus + $request->homework
    + $request->oral + $request->test1
    +$request->test2 + $request->exam_med
    +$request->exam_final);
    if ($aggregrate > 50){
    $mark->state = 1;
    }
    else {
        $mark->state = 0;
    }
    $mark->student_id = $student_id;
    $mark->subject_id = $request->subject_id;
    $mark->save();

    return response()->json(['succusssss']);

}




}
