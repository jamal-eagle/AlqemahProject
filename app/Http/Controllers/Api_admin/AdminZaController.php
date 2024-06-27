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

class AdminZaController extends Controller
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
    $num_order_for_course = Order::where('course_id', $course_id)->count();

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















    /**********************************جدوى**********************************/

    //المبلغ الذي حصل عليه المعهد من دفعات الطلاب للقسط
    // public function money_from_fee()
    // {
    //     // route::get('/money_from_fee', [AdminZaController::class, 'money_from_fee']);

    //     $all_pays = Pay_Fee::where('course_id', null)->sum('amount_money');
    //     return $all_pays;
    // }
    public function money_from_fee(Request $request)
{
    // route::get('/money_from_fee', [AdminZaController::class, 'money_from_fee']);
    
    
    

    $query = Pay_Fee::query();

    // تصفية حسب اليوم
    if ($request->has('day')) {
        $query->whereDay('date', $request->day);
    }

    // تصفية حسب الشهر
    if ($request->has('month')) {
        $query->whereMonth('date', $request->month);
    }

    // تصفية حسب السنة
    if ($request->has('year')) {
        $query->whereYear('date', $request->year);
    }

    if ($request->year_studey) {
    //    $user = User::where('year',$request->year_studey)->where('user_type','student')->get(student.id)

    }

    // تصفية المدفوعات التي ليس لها course_id
    $query->where('course_id', null);

    // حساب المجموع
    $all_pays = $query->sum('amount_money');

    return response()->json(['total_amount' => $all_pays]);
}



    //مجموع دفعات أقساط الطلاب///////////////////////////////////////
    //مجموع دفعات الدورات
    //مجموع البوفيه
    //مجموع النقل



    //معاشات الأساتذة
    //معاشات الموظفين
    //سلف 
    //مصاريف
    
    
    
}
