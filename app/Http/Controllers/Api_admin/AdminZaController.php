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
use App\Models\File_Archive;
use App\Models\File_course;
use App\Models\Image_Archive;
use App\Models\Maturitie;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Pay_Fee;
use App\Models\Program_Student;

class AdminZaController extends BaseController
{
    //j
    public function desplay_all_student_regester($year)
{
    $student = User::where('year',$year)->where('user_type', 'student')->with('student')->get();
    return response()->json([$student,'all student regester here']);
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

public function desplay_section_and_student($class_id)
{
    $classs = Classs::find($class_id);
    if(!$classs)
    {
        return response()->json(['the classs not found']);
    }
    $section = $classs->section;
    $student  =  Section::with('student.user')->find($section);
    return response()->json([$student]);

}

public function desplay_all_employee_and_others()
{
    $academy = Academy::find(1);
    $employee = Employee::where('year',$academy->year)->where('status',1)->get()->all();
    return response()->json([$employee]);

}

public function getWeeklyTeacherSchedule($teacher_id)
{
    $teacher = Teacher::find($teacher_id);
    if (!$teacher) {
        return response()->json(['message' => 'Teacher not found'], 404);
    }

    // استرجاع الجدول الزمني للأستاذ
    $schedules = Teacher_Schedule::where('teacher_id', $teacher_id)
                                ->orderBy('day_of_week')
                                ->orderBy('start_time')
                                ->get();

    if ($schedules->isEmpty()) {
        return response()->json(['message' => 'No schedule found for this teacher'], 404);
    }

    // تنظيم الجدول حسب أيام الأسبوع
    $weekly_schedule = [
        'Sunday' => [],
        'Monday' => [],
        'Tuesday' => [],
        'Wednesday' => [],
        'Thursday' => [],
    ];

    foreach ($schedules as $schedule) {
        $weekly_schedule[$schedule->day_of_week][] = [
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
        ];
    }

    return response()->json(['weekly_schedule' => $weekly_schedule], 200);
}

public function desplay_maturitie_for_teacher($teacher_id,$year,$month)
{
    $teacher = Teacher::find($teacher_id);
    //$mu = $teacher->with('maturitie');
    if(!$teacher)
    {
        return response()->json(['the teacher not found']);
    }
    $num_work_hour = $this->getteacherworkhour($teacher_id , $year , $month);
    $basic_salary = $num_work_hour * $teacher->cost_hour;
    $solfa = 0;
    $maturitie = $teacher->maturitie;
    foreach($maturitie as $mut)
    {
        $solfa += $mut->amount;
    }
    $salary = $basic_salary - $solfa;
    
    return response()->json([$basic_salary,$maturitie,$solfa,$salary]);



}

public function desplay_maturitie_for_employee($employee_id,$year,$month)
{
    $employee = Employee::find($employee_id);
    if(!$employee)
    {
        return response()->json(['the employee not found']);
    }
    $basic_salary = $employee->salary;
    $solfa = 0;
    $maturitie = $employee->maturitie;
    foreach($maturitie as $mut)
    {
        $solfa += $mut->amount;
    }
    $salary = $basic_salary - $solfa;

    return response()->json([$basic_salary,$solfa,$salary]);
}

//إعطاء موعد لطلب تسجيل في المعهد
public function GiveDate(Request $request, $order_id)
{
    if (Appointment::where('order_id', $order_id)->exists()) {
        return 'he has Appointment';
    }

    $validate = Validator::make($request->all(), [
        "date" => "required|date_format:Y-m-d|after:today"
    ]);

    if ($validate->fails()) {
        return $this->responseError(['errors' => $validate->errors()]);
    }

    $dateParts = explode('-', $request->date);
    if (!checkdate($dateParts[1], $dateParts[2], $dateParts[0])) {
        return $this->responseError(['errors' => ['date' => 'The date is not valid.']]);
    }

    $new = new Appointment;

    $new->date = $request->date;
    $new->order_id = $order_id;

    $new->save();

    return 'Appointment created successfully';
}

public function programe_week($section_id)
{
    // $student = Student::where('user_id', auth()->user()->id)->first();
    // $section_id = $student->section_id;
    //$programe = Program_Student::where('section_id', $student->section_id)->get();
    $programe = Program_Student::all();

    if ($programe) {
        $result = [];

        foreach ($programe as $p) {
            if ($p->section_id == $section_id) {
                $img = Image::all();
                foreach ($img as $i) {
                    if ($p->id == $i->program_student_id) {
                        $imagePath = str_replace('\\', '/', public_path().'/upload/'.$i->path);
                        if (file_exists($imagePath)) {
                            $i->image_file_url = asset('/upload/' . $i->path);
                            $result[] = [
                                // 'path' => $imagePath,
                                'image_info' => $i,
                                'program' => $p
                            ];
                        }
                    }
                }
            }
        }

        if (!empty($result)) {
            return response()->json([
                'status' => 'true',
                'images' => $result
            ]);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'No images found'
            ]);
        }
    } else {
        return response()->json([
            'status' => 'false',
            'message' => 'Program not found for this student'
        ]);
    }
}


// public function register_student1(Request $request)
// {
//     $academy = Academy::find(1);
//     /***حساب الطالب***/
//     // $validator3 = Validator::make($request->all(), [
//     //     'first_name_s' => 'required',
//     //     'last_name_s' => 'required|string',
//     //     'father_name' => 'required|string',
//     //     'mother_name' => 'required|string',
//     //     'birthday' => 'required|date',
//     //     'gender'=>'required',
//     //     'phone_s' => ['required', 'regex:/^\+963\s?9[0-9]{1}\s?[0-9]{3}\s?[0-9]{3}\s?[0-9]{3}$/'],
//     //     'address_s' => 'required',
//     //     'email_s'=>'required|email',
//     //     'password_s' => 'required|min:8',
//     //     'conf_password_s' => 'required|min:8|same:password',
//     // ]);

//     // if ($validator3->fails()) {
//     //     return $this->responseError(['errors' => $validator3->errors()]);
//     // }

//     $user = new User();

//     // $password_s  = $request->password_s;
//     $user->first_name = $request->first_name_s;
//     $user->last_name = $request->last_name_s;
//     $user->father_name = $request->father_name;
//     $user->mother_name = $request->mother_name;
//     $user->birthday = $request->birthday;
//     $user->gender = $request->gender;
//     $user->phone = $request->phone_s;
//     $user->address = $request->address_s;
//     $user->year = $academy->year;
//     $user->email = $request->email_s;
//     $user->password = Hash::make($request->password_s);
//     $user->conf_password = Hash::make($request->conf_password_s);
//     $user->user_type = 'student';
//     $user->save();

//     /***حساب الأهل***/
//     $validator = Validator::make($request->all(),[
//         'first_name_p'=>'required|string',
//         'last_name_p' => 'required|string',
//         'phone_p' => ['required', 'regex:/^\+963\s?9[0-9]{1}\s?[0-9]{3}\s?[0-9]{3}\s?[0-9]{3}$/'],
//         'address_p' => 'required',
//         'email_p'=>'required|email',
//         'password_p' => 'required|min:8',
//         'conf_password_p' => 'required|min:8|same:password',
//     ]);

//     if ($validator->fails()) {
//         return $this->responseError(['errors' => $validator->errors()]);
//     }

//     $parentt =new Parentt();
//     $parentt->first_name = $request->first_name_p;
//     $parentt->last_name = $request->last_name_p;
//     $parentt->phone = $request->phone_p;
//     $parentt->address = $request->address_p;
//     $parentt->email = $request->email_p;
//     if (!Parentt::where('email',$request->email)) {
//         $parentt->password = Hash::make($request->password_p);
//     $parentt->conf_password = Hash::make($request->conf_password_p);

//     $parentt->save();
//     // return response()->json([$parentt->email, $parentt->password]);
//     }

//     else {
//         $parentt->id = Parentt::where('email',$request->email)->first();
//     }
    

//     /***سجل للطالب شعبة صف..***/
//     $validator1 = Validator::make($request->all(), [
//         'school_tuition' => 'required',
//         'class_id' => 'required',
//         'section_id' => 'required',
//         // 'parentt_id' => 'required',
//         'student_type'=>'required',
//     ]);

//     if ($validator1->fails()) {
//         return $this->responseError(['errors' => $validator1->errors()]);
//     }

//     // إنشاء سجل الطالب الجديد
//     $student = new Student();
//     $student->school_tuition = $request->school_tuition;
//     $student->user_id = $user->id;
//     $student->class_id = $request->class_id;
//     $student->section_id = $request->section_id;
//     $student->parentt_id = $parentt->id;
//     $student->student_type = $request->student_type;

//     // تعيين التصنيف إذا كان الطالب من فئة البكالوريا
//     if ($request->student_type == 0 ) {
//         $validator2 = Validator::make($request->all(), [
//             'calssification' => 'required|in:0,1', // 0 للعلمي، 1 للأدبي
//         ]);

//         if ($validator2->fails()) {
//             return $this->responseError(['errors' => $validator2->errors()]);
//         }

//         $student->calssification = $request->calssification;
//     } else {
//         $student->calssification = null;
//     }

//     $student->save();

//     // إرجاع بيانات الدخول
//     return response()->json([$user, $student, $parentt]);
// }


    public function edit_year(Request $request,$id)
{
    $info = Academy::find($id);

    $info->year = $request->year ?? $info->year;

    $info->save();

    return $info;
    
}


public function add_course(Request $request)
{
    // Route::post('add_course' ,[AdminZaController::class,'add_course']);

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
        'Minimum_win' => 'required|integer|min:0',
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
    $course->Minimum_win = $request->Minimum_win;
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
    // route::get('display_info_course/{course_id}', [AdminZaController::class, 'display_info_course']);

    $course = Course::where('id', $course_id)
        ->with('teacher.user')
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
    $num_order_for_course = Order::where('course_id', $course_id)->where('student_type','11')->count();

    // المبلغ الذي جمعه المعهد من الطلاب المسجلين
    $Money = $num_order_for_course * $course->cost_course;

    // مصاريف الدورة الكلية
    $expenses = Expenses::where('course_id', $course_id)->sum('total_cost') ?? 0;

    // النسبة التي يحصل عليها المعهد بعد خصم نسبة المدرس
    $institute_percentage = 100 - $course->percent_teacher;

    // المبلغ الذي يجب جمعه ليغطي المصاريف ويحقق الربح المطلوب
    $required_money_to_open = $expenses + $course->Minimum_win;  // إضافة الربح المطلوب 500000 إلى المصاريف

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

    //إضافة موجه
public function add_monetor(Request $request)
{
    // Route::post('/add_monetor' ,[AdminZaController::class,'add_monetor']);

    $academy = Academy::find(1);
    
    $validator = Validator::make($request->all(), [
        'first_name' => 'required',
        'last_name' => 'required|string',
        'father_name' => 'required|string',
        'mother_name' => 'required|string',
        'birthday' => 'required|date',
        'gender'=>'required',
        'phone' => 'required',
        'address' => 'required',
        'email'=>'required|email',
        'password' => 'required|min:8',
        'conf_password' => 'required|min:8',
        'salary' => 'required',
    ]);

    if ($validator->fails()) {
        return $this->responseError(['errors' => $validator->errors()]);
    }

    $monetor = new User;

    $password  = $request->password;
    $monetor->first_name = $request->first_name;
    $monetor->last_name = $request->last_name;
    $monetor->father_name = $request->father_name;
    $monetor->mother_name = $request->mother_name;
    $monetor->birthday = $request->birthday;
    $monetor->gender = $request->gender;
    $monetor->phone = $request->phone;
    $monetor->address = $request->address;
    $monetor->year = $academy->year;
    $monetor->email = $request->email;
    $monetor->password = Hash::make($password);
    $monetor->conf_password = Hash::make($password);
    $monetor->user_type = 'monetor';
    $monetor->save();

    $employee = new Employee();

    $employee->first_name = $monetor->first_name;
    $employee->last_name = $monetor->last_name;
    $employee->phone = $monetor->phone;
    $employee->address = $monetor->address;
    $employee->salary = $request->salary;
    $employee->year = $academy->year;
    $employee->email = $monetor->email;
    $employee->password = Hash::make($password);
    $employee->type = $monetor->user_type;

    $employee->save();
    return 'good add';

}

//إضافة محاسب
public function add_accounting(Request $request)
{
    // Route::post('/add_accounting',[AdminZaController::class,'add_accounting']);
    $academy = Academy::find(1);
    
    $validator = Validator::make($request->all(), [
        'first_name' => 'required',
        'last_name' => 'required|string',
        'father_name' => 'required|string',
        'mother_name' => 'required|string',
        'birthday' => 'required|date',
        'gender'=>'required',
        'phone' => 'required',
        'address' => 'required',
        'email'=>'required|email',
        'password' => 'required|min:8',
        'conf_password' => 'required|min:8',
        'salary' => 'required',
    ]);

    if ($validator->fails()) {
        return $this->responseError(['errors' => $validator->errors()]);
    }

    $monetor = new User;

    $password  = $request->password;
    $monetor->first_name = $request->first_name;
    $monetor->last_name = $request->last_name;
    $monetor->father_name = $request->father_name;
    $monetor->mother_name = $request->mother_name;
    $monetor->birthday = $request->birthday;
    $monetor->gender = $request->gender;
    $monetor->phone = $request->phone;
    $monetor->address = $request->address;
    $monetor->year = $academy->year;
    $monetor->email = $request->email;
    $monetor->password = Hash::make($password);
    $monetor->conf_password = Hash::make($password);
    $monetor->user_type = 'accounting';
    $monetor->save();

    $employee = new Employee();

    $employee->first_name = $monetor->first_name;
    $employee->last_name = $monetor->last_name;
    $employee->phone = $monetor->phone;
    $employee->address = $monetor->address;
    $employee->salary = $request->salary;
    $employee->year = $academy->year;
    $employee->email = $monetor->email;
    $employee->password = Hash::make($password);
    $employee->type = $monetor->user_type;

    $employee->save();
    return 'good add';

}

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

    //إضافة دفعة لطالب محدد
    public function add_pay(Request $request, $student_id)
    {
        // route::post('add_pay/{student_id}', [AdminZaController::class, 'add_pay']);
    
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string',
            'date' => 'nullable|date',
            'amount_money' => 'required|numeric',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $pay = new Pay_Fee();
        $pay->type = $request->type;
        $pay->date = $request->date ?? now();

        $all_pays = Pay_Fee::where('student_id', $student_id)->where('course_id', null)->sum('amount_money');
        $total_fee = Student::where('id', $student_id)->value('school_tuition');
        $remaining_fee_before_pay_now = $total_fee - $all_pays;

        if ($request->amount_money > $remaining_fee_before_pay_now) {
            return response()->json(['errors' => 'The amount money big'], 422);
        }

        $pay->amount_money = $request->amount_money;
        $pay->student_id = $student_id;
        
        // $all_pays = Pay_Fee::where('student_id', $student_id)->sum('amount_money');
        $total_paid = $all_pays + $pay->amount_money;
    
        // $total_fee = Student::where('id', $student_id)->value('school_tuition');
        
        $remaining_fee = $total_fee - $total_paid;
        
        $pay->remaining_fee = $remaining_fee;
        
        $pay->save();
        
        return response()->json([
            'pay' => $pay,
            'total_paid' => $total_paid,
            'remaining_fee' => $remaining_fee,
        ]);
    }

    public function add_pay_course(Request $request, $student_id,$course_id)
    {
        // route::post('add_pay_course/{student_id}/{course_id}', [AdminZaController::class, 'add_pay_course']);

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string',
            'date' => 'nullable|date',
            'amount_money' => 'required|numeric',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $pay = new Pay_Fee();
        $pay->type = $request->type;
        $pay->date = $request->date ?? now();

        $all_pays = Pay_Fee::where('student_id', $student_id)->where('course_id',$course_id)->sum('amount_money');
        $total_fee = Course::where('id',$course_id)->value('cost_course');
        $remaining_fee_before_pay_now = $total_fee - $all_pays;

        if ($request->amount_money > $remaining_fee_before_pay_now) {
            return response()->json(['errors' => 'The amount money big'], 422);
        }
        
        $pay->amount_money = $request->amount_money;
        $pay->student_id = $student_id;
        $pay->course_id = $course_id;
        
        // $all_pays = Pay_Fee::where('student_id', $student_id)->where('course_id',$course_id)->sum('amount_money');
        $total_paid = $all_pays + $pay->amount_money;
    
        // $total_fee = Course::where('id',$course_id)->value('cost_course');

        //المبلغ المتبقي بعد دفع دفعة اليوم
        $remaining_fee = $total_fee - $total_paid;
        
        $pay->remaining_fee = $remaining_fee;
        
        $pay->save();
        
        return response()->json([
            'pay' => $pay,
            'total_paid' => $total_paid,
            'remaining_fee' => $remaining_fee,
        ]);

    }

    //عرض شعب صف معين
    public function display_section_for_class($class_id)
    {
        // route::get('display_section_for_class/{class_id}', [AdminZaController::class, 'display_section_for_class']);

        $section = Section::where('class_id', $class_id)->get();
        return $section;
    }

    //عرض طلاب شعبة معينة
    public function display_student_in_section($section_id)
    {
       $student =  Student::where('section_id', $section_id)->with('user')->get();
       return $student;

    }

    //عرض طلبات التسجيل بالمعهد
public function order_on_course($course_id)
{
    $order = Order::where('course_id', $course_id)->where('student_type','10')->get();

    return $order;
}

    //الموافقة على طلب تسجيل في كورس
    public function ok_order_course($order_id)
    {
        $order = Order::where('id', $order_id)->first();

        $order->student_type = '11';

        $order->save();

        return $order;
    }

    //رفض طلب تسجيل في دورة
    public function no_order_course($order_id)
    {
        $order = Order::where('id', $order_id)->first();

        $order->student_type = '12';

        $order->save();

        return $order;

    }
    //عرض الطلاب في دورة
    public function display_student_in_course($course_id)
    {
        $order = Order::where('course_id', $course_id)->where('student_type','11')->get();

        return $order;

    }















// وظيفة لإضافة سلفة لمعلم باستخدام مُعرف المعلم في الـ URL
public function addTeacherMaturitie(Request $request, $idteacher)
{
    $validator = Validator::make($request->all(), [
        'amount' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // التحقق من وجود المعلم في قاعدة البيانات
    $teacher = Teacher::find($idteacher);
    if (!$teacher) {
        return response()->json(['message' => 'Teacher not found'], 404);
    }

    $maturite = new Maturitie();
    $maturite->amount = $request->amount;
    $maturite->teacher_id = $idteacher;
    $maturite->save();

    return response()->json(['success' => 'Maturitie added successfully for teacher']);
}

// وظيفة لإضافة سلفة لموظف باستخدام مُعرف الموظف في الـ URL
public function addEmployeeMaturitie(Request $request, $idemployee)
{
    $validator = Validator::make($request->all(), [
        'amount' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }
    // التحقق من وجود المعلم في قاعدة البيانات
    $employee = Employee::find($idemployee);
    if (!$employee) {
        return response()->json(['message' => 'Employee not found'], 404);
    }

    $maturite = new Maturitie();
    $maturite->amount = $request->amount;
    $maturite->employee_id = $idemployee;
    $maturite->save();

    return response()->json(['success' => 'Maturitie added successfully for employee']);
}
    /**********************************جدوى**********************************/

    //المبلغ الذي حصل عليه المعهد من دفعات الطلاب للقسط حسب يوم أو شهر أو سنة أو عام دراسي أو دمج بيناتون
    public function money_from_fee(Request $request)
{
    // route::get('/money_from_fee', [AdminZaController::class, 'money_from_fee']);
    $query = Pay_Fee::query();

    // تصفية حسب اليوم
    if ($request->has('day') && !empty($request->day)) {
        $query->whereDay('date', $request->day);
    }

    // تصفية حسب الشهر
    if ($request->has('month') && !empty($request->month)) {
        $query->whereMonth('date', $request->month);
    }

    // تصفية حسب السنة
    if ($request->has('year') && !empty($request->year)) {
        $query->whereYear('date', $request->year);
    }

    // تصفية حسب السنة الدراسية
    if ($request->has('year_studey') && !empty($request->year_studey)) {
        // جلب المستخدمين الذين يطابقون السنة الدراسية المحددة والنوع "طالب"
        $users = User::where('year', $request->year_studey)
                     ->where('user_type', 'student')
                     ->with('student:id,user_id')
                     ->get();

        // التأكد من جلب الطلاب بشكل صحيح
        if ($users->isEmpty()) {
            return response()->json(['message' => 'No students found'], 404);
        }

        // استخراج معرفات الطلاب
        $studentIds = $users->map(function($user) {
            return $user->student ? $user->student->id : null;
        })->filter()->toArray();

        // إضافة شرط السنة الدراسية إلى الاستعلام
        $query->whereIn('student_id', $studentIds);
    }

    // تصفية المدفوعات التي ليس لها course_id
    $query->where('course_id', null);

    // الحصول على قائمة المدفوعات
    $pays = $query->get();

    // حساب المجموع
    $total_amount = $pays->sum('amount_money');

    return response()->json([
        'total_amount' => $total_amount,
        'pays' => $pays,
    ]);
}
    
    //المبلغ الذي حصل عليه المعهد من دفعات الطلاب للدورة حسب يوم أو شهر أو سنة أو عام دراسي أو دمج بيناتون
    public function money_from_all_course(Request $request)
     {
        $query = Pay_Fee::query();

        // تصفية حسب اليوم
        if ($request->has('day') && !empty($request->day)) {
            $query->whereDay('date', $request->day);
        }

        // تصفية حسب الشهر
        if ($request->has('month') && !empty($request->month)) {
            $query->whereMonth('date', $request->month);
        }

        // تصفية حسب السنة
        if ($request->has('year') && !empty($request->year)) {
            $query->whereYear('date', $request->year);
        }

        // تصفية حسب السنة الدراسية
        if ($request->has('year_studey') && !empty($request->year_studey)) {
            // جلب المستخدمين الذين يطابقون السنة الدراسية المحددة والنوع "طالب"
            $course = Course::where('year', $request->year_studey)->get();

            // التأكد من جلب الدورة بشكل صحيح
            if ($course->isEmpty()) {
                return response()->json(['message' => 'No Courses found'], 404);
            }

            // جلب المعرفات فقط
            $courseIds = $course->pluck('id');

            // إضافة شرط السنة الدراسية إلى الاستعلام
            $query->whereIn('course_id', $courseIds);

        }

        // if ($request->has('subject_name') && !empty($request->subject_name)) {

        //     $subject = Subject::where('name',$request->subject_name)

        //     $course = Course::where('subject_id', $request->subject)->get();

        //     // التأكد من جلب الدورة بشكل صحيح
        //     if ($course->isEmpty()) {
        //         return response()->json(['message' => 'No Courses found'], 404);
        //     }

        //     // جلب المعرفات فقط
        //     $courseIds = $course->pluck('id');

        //     // إضافة شرط السنة الدراسية إلى الاستعلام
        //     $query->whereIn('course_id', $courseIds);

        // }

        

        $query->whereNotNull('course_id');
        $pays = $query->get();

        // حساب المجموع
        $total_amount = $pays->sum('amount_money');

        return response()->json([
            'total_amount' => $total_amount,
            'pays' => $pays,
        ]);
        
     }






    //مجموع دفعات أقساط الطلاب///////////////////////////////////////
    //مجموع دفعات الدورات
    //مجموع البوفيه
    //مجموع النقل



    //معاشات الأساتذة
    //معاشات الموظفين
    //سلف 
    //مصاريف
    
    public function register_student1(Request $request)
{
    $academy = Academy::find(1);
    /***حساب الطالب***/
    $validator3 = Validator::make($request->all(), [
        'first_name_s' => 'required',
        'last_name_s' => 'required|string',
        'father_name' => 'required|string',
        'mother_name' => 'required|string',
        'birthday' => 'required|date',
        'gender'=>'required',
        'phone_s' => 'required',
        'address_s' => 'required',
        'email'=>'required|email',
        'password_s' => 'required|min:8',
        'conf_password_s' => 'required|min:8',
    ]);

    if ($validator3->fails()) {
        return $this->responseError(['errors' => $validator3->errors()]);
    }

    $user = new User();

    // $password_s  = $request->password_s;
    $user->first_name = $request->first_name_s;
    $user->last_name = $request->last_name_s;
    $user->father_name = $request->father_name;
    $user->mother_name = $request->mother_name;
    $user->birthday = $request->birthday;
    $user->gender = $request->gender;
    $user->phone = $request->phone_s;
    $user->address = $request->address_s;
    $user->year = $academy->year;
    $user->email = $request->email_s;
    $user->password = Hash::make($request->password_s);
    $user->conf_password = Hash::make($request->conf_password_s);
    $user->user_type = 'student';
    $user->save();

    /***حساب الأهل***/
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
    $parentt->first_name = $request->first_name_p;
    $parentt->last_name = $request->last_name_p;
    $parentt->phone = $request->phone_p;
    $parentt->address = $request->address_p;
    $parentt->email = $request->email_p;
    $parentt->year = $academy->year;
    if (!Parentt::where('email',$request->email)) {
        $parentt->password = Hash::make($request->password_p);
    $parentt->conf_password = Hash::make($request->conf_password_p);

    $parentt->save();
    // return response()->json([$parentt->email, $parentt->password]);
    }

    else {
        $parentt->id = Parentt::where('email',$request->email)->first();
    }
    

    /***سجل للطالب شعبة صف..***/
    $validator1 = Validator::make($request->all(), [
        'school_tuition' => 'required',
        'class_id' => 'required',
        'section_id' => 'required',
        // 'parentt_id' => 'required',
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
    $student->parentt_id = $parentt->id;
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
    return response()->json([$user, $student, $parentt]);
}


public function register(Request $request)
{
    $academy = Academy::find(1);

    // Validate student data
    $validatorStudent = Validator::make($request->all(), [
        'first_name_s' => 'required',
        'last_name_s' => 'required|string',
        'father_name' => 'required|string',
        'mother_name' => 'required|string',
        'birthday' => 'required|date',
        'gender' => 'required',
        // 'phone_s' => ['required', 'regex:/^\+963\s?9[0-9]{1}\s?[0-9]{3}\s?[0-9]{3}\s?[0-9]{3}$/'],
        'address_s' => 'required',
        'email' => 'required|email|unique:users',
        'password_s' => 'required|min:8',
        'conf_password_s' => 'required|min:8|same:password_s',
        'school_tuition' => 'required',
        'class_id' => 'required',
        'name_section' => 'required|string|exists:sections,num_section',
        // 'student_type' => 'required',
        // 'calssification' => 'required_if:student_type,0|in:0,1',
    ]);

    // Validate parent data
    $validatorParent = Validator::make($request->all(), [
        'first_name_p' => 'required|string',
        'last_name_p' => 'required|string',
        // 'phone_p' => ['required', 'regex:/^\+963\s?9[0-9]{1}\s?[0-9]{3}\s?[0-9]{3}\s?[0-9]{3}$/'],
        'address_p' => 'required',
        'email_p' => 'required|email',
        'password_p' => 'required|min:8',
        'conf_password_p' => 'required|min:8|same:password_p',
    ]);

    // Check if any validation errors occurred
    if ($validatorStudent->fails() || $validatorParent->fails()) {
        $errors = $validatorStudent->errors()->merge($validatorParent->errors());
        return $this->responseError(['errors' => $errors]);
    }

    // Create parent record
    $parentt = Parentt::where('email', $request->email_p)->first();
    if (!$parentt) {
        $parentt = new Parentt();
        $parentt->first_name = $request->first_name_p;
        $parentt->last_name = $request->last_name_p;
        if ($request->has('phone_p') && !empty($request->phone_p)) {
            $phone = $request->phone_p;
            if (!preg_match('/^(\+?963|0)?9\d{8}$/', $phone)) {
                return response()->json(['status' => 'error', 'message' => 'Invalid Syrian phone number'], 400);
            }
            $parentt->phone = $request->phone_p;
        }
        // $parentt->phone = $request->phone_p;

        $parentt->address = $request->address_p;
        $parentt->email = $request->email_p;
        $parentt->year = $academy->year;
        $parentt->password = Hash::make($request->password_p);
        $parentt->conf_password = Hash::make($request->conf_password_p);
    }

    // Create student record
    $user = new User();
    $user->first_name = $request->first_name_s;
    $user->last_name = $request->last_name_s;
    $user->father_name = $request->father_name;
    $user->mother_name = $request->mother_name;
    $user->birthday = $request->birthday;
    $user->gender = $request->gender;
    if ($request->has('phone_s') && !empty($request->phone_s)) {
        $phone = $request->phone_s;
        if (!preg_match('/^(\+?963|0)?9\d{8}$/', $phone)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Syrian phone number'], 400);
        }
        $user->phone = $request->phone_s;
    }
    // $user->phone = $request->phone_s;
    $user->address = $request->address_s;
    $user->year = $academy->year;
    $user->email = $request->email;
    $user->password = Hash::make($request->password_s);
    $user->conf_password = Hash::make($request->conf_password_s);
    $user->user_type = 'student';

    // Create student profile
    $student = new Student();
    $student->school_tuition = $request->school_tuition;
    $student->class_id = $request->class_id;

    // تحديد الشعبة للطالب
    $section = Section::where('num_section', $request->name_section)->where('class_id', $student->class_id)->first();
    if (!$section) {
        return response()->json([
            'status' => 'false',
            'message' => 'Subject not found for the specified class_id',
        ]);
    }
    $student->section_id = $section->id;

    // $student->student_type = $request->student_type;
    // if ($request->student_type != 0 && empty($request->calssification)) {
    //     $student->calssification = 2;
    // }
    // else {
    //     $student->calssification = $request->calssification;
    // }
    // $student->calssification = $request->student_type == 0 ? $request->calssification : null;
    
    
    $parentt->save();
    $user->save();
    $student->user_id = $user->id;
    $student->parentt_id = $parentt->id;
    $student->save();

    // Return response with created user, student, and parentt
    return response()->json(['user' => $user, 'student' => $student, 'parentt' => $parentt]);
}







// public function register2(Request $request,$academy_id){


//     $academy = Academy::find($academy_id);
//     $validator3 = Validator::make($request->all(), [
//         'first_name' => 'required',
//         'last_name' => 'required|string',
//         'father_name' => 'required|string',
//         'mother_name' => 'required|string',
//         'birthday' => 'required|date',
//         'gender'=>'required',
//         'phone' => 'required',
//         'address' => 'required',
//         'email'=>'required|email',
//         'password' => 'required|min:8',
//         'conf_password' => 'required|min:8',
//     ]);

//     if ($validator3->fails()) {
//         return $this->responseError(['errors' => $validator3->errors()]);
//     }

//     $user = new User();

//     $password  = $request->password;
//     $user->first_name = $request->first_name;
//     $user->last_name = $request->last_name;
//     $user->father_name = $request->father_name;
//     $user->mother_name = $request->mother_name;
//     $user->birthday = $request->birthday;
//     $user->gender = $request->gender;
//     $user->phone = $request->phone;
//     $user->address = $request->address;
//     $user->year = $academy->year;
//     $user->email = $request->email;
//     $user->password = Hash::make($password);
//     $user->conf_password = Hash::make($password);
//     $user->user_type = 'student';
//     $user->save();

//     $validator1 = Validator::make($request->all(), [
//         'school_tuition' => 'required',
//         'class_id' => 'required',
//         'section_id' => 'required',
//         'parentt_id' => 'required',
//         'student_type'=>'required',
//     ]);

//     if ($validator1->fails()) {
//         return $this->responseError(['errors' => $validator1->errors()]);
//     }

//     // إنشاء سجل الطالب الجديد
//     $student = new Student();
//     $student->school_tuition = $request->school_tuition;
//     $student->user_id = $user->id;
//     $student->class_id = $request->class_id;
//     $student->section_id = $request->section_id;
//     $student->parentt_id = $request->parentt_id;
//     $student->student_type = $request->student_type;

//     // تعيين التصنيف إذا كان الطالب من فئة البكالوريا
//     if ($request->student_type == 0 ) {
//         $validator2 = Validator::make($request->all(), [
//             'calssification' => 'required|in:0,1', // 0 للعلمي، 1 للأدبي
//         ]);

//         if ($validator2->fails()) {
//             return $this->responseError(['errors' => $validator2->errors()]);
//         }

//         $student->calssification = $request->calssification;
//     } else {
//         $student->calssification = null;
//     }

//     $student->save();

//     // إرجاع بيانات الدخول
//     return response()->json([$user->email, $password]);
// }





    
}
