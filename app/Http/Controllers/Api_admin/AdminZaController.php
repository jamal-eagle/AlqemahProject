<?php

namespace App\Http\Controllers\Api_admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\MyNotification;
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
use App\Models\Teacher_subject;
use App\Models\Fee_School;
use App\Models\Post;
use App\Models\Teacher_section;
use App\Models\Bus;
use App\Models\Salary;
use App\Models\Actions_log;

class AdminZaController extends BaseController
{
    //j
    public function desplay_all_student_regester()
    {
        $academy = Academy::find(1);
        $student = User::where('year',$academy->year)->where('user_type', 'student')->where('status','1')->with('student')->get();
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

        if ($note_student->save()) {
            $user = User::find($student->user_id);  // استبدل بمعرف المستخدم المناسب
            $parentt = Parentt::find($student->parentt_id);
        $message = 'This is a test notification!';
        if ($user) {
            $user->notify(new MyNotification($message));
        }
        
        if ($parentt) {
            $parentt->notify(new MyNotification($message));
        }
        }

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

    // استرجاع الجدول الزمني للأستاذ مع تفاصيل الشعبة
    $schedules = Teacher_Schedule::with('section')
                                ->where('teacher_id', $teacher_id)
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
            'section' => $schedule->section ? $schedule->section->num_section : 'N/A',
        ];
    }

    return response()->json(['weekly_schedule' => $weekly_schedule], 200);
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

// public function programe_week($section_id)
// {
 //     // $student = Student::where('user_id', auth()->user()->id)->first();
 //     // $section_id = $student->section_id;
 //     //$programe = Program_Student::where('section_id', $student->section_id)->get();
 //     $programe = Program_Student::all();

 //     if ($programe) {
 //         $result = [];

 //         foreach ($programe as $p) {
 //             if ($p->section_id == $section_id) {
 //                 $img = Image::all();
 //                 foreach ($img as $i) {
 //                     if ($p->id == $i->program_student_id) {
 //                         $imagePath = str_replace('\\', '/', public_path().'/upload/'.$i->path);
 //                         if (file_exists($imagePath)) {
 //                             $i->image_file_url = asset('/upload/' . $i->path);
 //                             $result[] = [
 //                                 // 'path' => $imagePath,
 //                                 'image_info' => $i,
 //                                 'program' => $p
 //                             ];
 //                         }
 //                     }
 //                 }
 //             }
 //         }

 //         if (!empty($result)) {
 //             return response()->json([
 //                 'status' => 'true',
 //                 'images' => $result
 //             ]);
 //         } else {
 //             return response()->json([
 //                 'status' => 'false',
 //                 'message' => 'No images found'
 //             ]);
 //         }
 //     } else {
 //         return response()->json([
 //             'status' => 'false',
 //             'message' => 'Program not found for this student'
 //         ]);
 //     }
// }

public function programe_week($section_id)
{
    // $student = Student::where('user_id', auth()->user()->id)->first();

    // if (!$student) {
    //     return response()->json(['status' => 'false', 'message' => 'Student not found'], 404);
    // }

    // $section_id = $student->section_id;
    $programs = Program_Student::where('section_id', $section_id)->get();

    if ($programs->isEmpty()) {
        return response()->json(['status' => 'false', 'message' => 'Program not found for this student'], 404);
    }

    $result = [];

    foreach ($programs as $program) {
        $images = Image::where('program_student_id', $program->id)->get();

        foreach ($images as $image) {
            $imagePath = public_path('/upload/' . $image->path);

            if (file_exists($imagePath)) {
                $program->image_file_url = asset('/upload/' . $image->path);
                $result[] = [
                    'program' => $program,
                    // 'image_info' => $image
                ];
            }
        }
    }

    if (!empty($result)) {
        // return response()->json($result);

        // return $result;
        return $programs;
    } else {
        return response()->json(['status' => 'false', 'message' => 'No images found'], 404);
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

public function display_year()
{
    $info = Academy::find('1');
    
    return $info->year;
}

public function display_resolve()
{
    $info = Academy::find('1');

    return response()->json(['resolve_brother' =>  $info->resolve_brother, 'resolve_martyr' => $info->resolve_martyr, 'resolve_Son_teacher' => $info->resolve_Son_teacher]);
}

public function display_fee_class($year)
{
    $fee = Fee_School::where('year', $year)->with('classs')->get();

    return $fee;

}

public function display_info_academy()
{
    $info = Academy::find('1');
    
    return $info;
}


public function edit_year(Request $request)
{
    $info = Academy::find('1');

    //عدلنا العام الدراسي
    $info->year = $request->year ?? $info->year;

    //إيقاف كل المستخدمين
    User::where('status', '1')->where('user_type', '!=', 'admin')->update(['status' => '0']);

    //إنشاء أرشيف

    //إذا أنشأ مادة فيأمشئ أرشيف لها تلقائياً

    //إنشاء قسط جديد

    $info->save();

    return $info;

}

public function edit_fee_class(Request $request, $year, $class_id)
{
    $validator = Validator::make($request->all(), [
        'amount' => 'required|numeric',
    ]);

    if ($validator->fails()) {
        return $this->responseError(['errors' => $validator->errors()]);
    }

    $have = Fee_School::where('year', $year)->where('class_id', $class_id)->first();

    if ($have) {
        $have->amount = $request->amount;
        if ($have->save()) {
            return response()->json(['message' => 'update fee successfully'], 200);
        }
        
    }

    $fee = new Fee_School();

    $fee->year = $year;
    $fee->amount = $request->amount;
    $fee->class_id = $class_id;

    if ($fee->save()) {
        return response()->json(['message' => 'add fee successfully'], 200);
    }

    return response()->json(['message' => 'add fee fail'], 200);

}


public function edit_resolve(Request $request, $year)
{
    $info = Academy::where('year', $year)->first();

    if (!$info) {
        return response()->json(['message' => 'you do not have this year'], 200);
    }

    $validator = Validator::make($request->all(), [
        'resolve_brother' => 'numeric|between:0,100',
        'resolve_martyr' => 'numeric|between:0,100',
        'resolve_Son_teacher' => 'numeric|between:0,100',
    ]);

    if ($validator->fails()) {
        return $this->responseError(['errors' => $validator->errors()]);
    }

    $info->resolve_brother = $request->resolve_brother;
    $info->resolve_martyr = $request->resolve_martyr;
    $info->resolve_Son_teacher = $request->resolve_Son_teacher;

    if ($info->save()) {
        return response()->json(['message' => 'you update resolve successfully'], 200);
    }
    return response()->json(['message' => 'you update resolve fail'], 200);

}

public function edit_info_academy(Request $request)
{
    $info = Academy::find('1');

    $validator = Validator::make($request->all(), [
        'name' => 'nullable|string',
        // 'phone' => 'nullable|numeric',
        'address' => 'nullable|string',
        'facebook_link' => 'nullable|string',
        'description' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return $this->responseError(['errors' => $validator->errors()]);
    }

    if ($request->has('name') && !empty($request->name)) {
        $info->name = $request->name;
    }

    if ($request->has('phone1') && !empty($request->phone1)) {
        $phone1 = $request->phone1;
    
        // تعبير عادي للأرقام السورية
        if (!preg_match('/^(\+?963|0)?9\d{8}$|^0(11|21|31|41|51|61|71|81|91)\d{7}$/', $phone1)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Syrian phone number'], 400);
        }
    
        $info->phone1 = $request->phone1;
    }

    if ($request->has('phone2') && !empty($request->phone2)) {
        $phone2 = $request->phone2;
    
        // تعبير عادي للأرقام السورية
        if (!preg_match('/^(\+?963|0)?9\d{8}$|^0(11|21|31|41|51|61|71|81|91)\d{7}$/', $phone2)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Syrian phone number'], 400);
        }
    
        $info->phone2 = $request->phone2;
    }
    
    if ($request->has('address') && !empty($request->address)) {
        $info->address = $request->address;
    }

    if ($request->has('facebook_link') && !empty($request->facebook_link)) {
        $info->facebook_link = $request->facebook_link;
    }

    if ($request->has('description') && !empty($request->description)) {
        $info->description = $request->description;
    }

    if ($info->save()) {
        return response()->json(['message' => 'you update info successfully'], 200);
    }

    return response()->json(['message' => 'you update info fail'], 200);

}


// public function add_course(Request $request)
// {
//     // Route::post('add_course' ,[AdminZaController::class,'add_course']);

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
//         'Minimum_win' => 'required|integer|min:0',
//         'percent_teacher' => 'required|numeric|between:0,100',
//         'class_id' => 'required|exists:classses,id',
//         'teacher_id' => 'required|exists:teachers,id',
//         'name_subject' => 'required|string|exists:subjects,name',
//         'description_publish' => 'nullable|string',
//         'path' => 'nullable|mimes:png,jpg,jpeg,gif,pdf,docx,txt',
//         'expenses.*.date' => 'required|date',
//         'expenses.*.product' => 'required|string',
//         'expenses.*.cost_one_piece' => 'required|numeric',
//         'expenses.*.num_product' => 'required|integer|min:1',
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
//     $diffInDays = $start_date->diffInDays($finish_date) + 2;

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
//     $course->Minimum_win = $request->Minimum_win;
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

//     // إضافة المصاريف المتعددة
//     if ($request->has('expenses')) {
//         foreach ($request->expenses as $expenseData) {
//             $expenses = new Expenses;
//             $expenses->date = $expenseData['date'];
//             $expenses->product = $expenseData['product'];
//             $expenses->cost_one_piece = $expenseData['cost_one_piece'];
//             $expenses->num_product = $expenseData['num_product'];
//             $expenses->total_cost = $expenses->cost_one_piece * $expenses->num_product;
//             $expenses->year = $academy->year;
//             $expenses->course_id = $course->id;
//             $expenses->save();
//         }
//     }

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
//                 'data_image' => $image,
//                 'expenses' => $expenses,

//             ], 200);
//         }

//         return response()->json([
//             'status' => 'true',
//             'message' => 'Course with publish text success',
//             'course' => $course,
//         ], 200);
//     }

//     return response()->json([
//         'status' => 'true',
//         'message' => 'Course created successfully',
//         'course' => $course
//     ], 200);
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
        ], 400);
    }

    // التحقق من فرق الأيام بين start_date و finish_date
    $start_date = \Carbon\Carbon::parse($request->start_date);
    $finish_date = \Carbon\Carbon::parse($request->finish_date);
    $diffInDays = $start_date->diffInDays($finish_date) + 2;

    if ($request->num_day > $diffInDays) {
        return response()->json([
            'status' => 'false',
            'message' => 'The num_day should not be greater than the difference in days between start_date and finish_date',
        ], 400);
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
        ], 400);
    }
    $course->subject_id = $subject->id;

    // تحديد المدرس للدورة
    $course->teacher_id = $request->teacher_id;

    // حفظ الدورة
    if ($course->save()) {
        $allExpenses = [];
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
                 // إضافة المصروف إلى المصروفات الكلية
            $allExpenses[] = $expenses;
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
                    'expenses' => $allExpenses ?? []
                ], 200);
            }

            return response()->json([
                'status' => 'true',
                'message' => 'Course with publish text success',
                'course' => $course,
            ], 200);
        }

        return response()->json([
            'status' => 'true',
            'message' => 'Course created successfully',
            'course' => $course
        ], 200);
    } else {
        return response()->json([
            'status' => 'false',
            'message' => 'Failed to create course',
        ], 500);
    }
}

// public function all_teatcher()
//     {
//         // $teatcher = Teacher::with('user')->get();
//         // return $teatcher;

//         $academy = Academy::find(1);
//         $teachers = User::where('user_type', 'teacher')->where('year',$academy->year)
//                     ->with('teacher')
//                     ->orderBy('first_name')
//                     ->orderBy('last_name')
//                     ->orderBy('father_name')
//                     ->get();
//     return $teachers;

//     }

// //مدرسي مادة محدد لصف محدد
// public function display_teacher_for_course($name_subject, $class_id)
// {
//     $academy = Academy::find(1);
//     $subject = Subject::where('name', $name_subject)->where('class_id', $class_id)->first();

//     if (!$subject) {
//         return 'you do not have this name subject or tha class does not have this subject';
//     }

//     $teacher_subject = Teacher_subject::where('subject_id',$subject->id)->get();

//     $result = [];
//     // foreach ($teacher_subject as $t) {
//     //     $teacher = Teacher::where('id',$t->teacher_id)->first();
//     //     $result[] = [
//     //         $teacher->user,
//     //     ];
//     // }
//     foreach ($teacher_subject as $t) {
//         $teacher = Teacher::where('id',$t->teacher_id)->first();
//         $user = User::where('id', $teacher->user_id)->first();
//         if ($user->year == $academy->year) {
//             $result[] = [
//                 $user,
//             ];
//         }

//     }
//     return $result;

// }

public function display_teacher_for_course($name_subject, $class_id)
{
    $academy = Academy::find(1);
    $subject = Subject::where('name', $name_subject)->where('class_id', $class_id)->first();

    if (!$subject) {
        return 'you do not have this name subject or the class does not have this subject';
    }

    $teacher_subject = Teacher_subject::where('subject_id', $subject->id)->get();

    $result = [];
    foreach ($teacher_subject as $t) {
        $teacher = Teacher::where('id', $t->teacher_id)->first();
        $user = User::where('id', $teacher->user_id)->with('teacher')->first();
        if ($user->year == $academy->year) {
            $result[] = $user;
        }
    }

    // Sort the results by first_name, last_name, and father_name

    $sorted_result = collect($result)->sortBy([
        ['first_name', 'asc'],
        ['last_name', 'asc'],
        ['father_name', 'asc'],
    ])->values()->all();

    return $sorted_result;
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
// return $num_order_for_course;
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
    if ($num_order_for_course > $num_students_required) {
        $num_students_remaining = 0;
    }
    else {
        $num_students_remaining = $num_students_required - $num_order_for_course;
    }


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

        //كلشي تحت لتغير حالة الدورة من قيد الدراسة إلى مفتوحة
        //عدد الطلاب المسجلين في الدورة
        $num_order_for_course = Order::where('course_id',$order->course_id)->where('student_type','11')->count();

        $course = Course::find($order->course_id);

        //المبلغ الذي جمعه المعهد من الطلاب المسجلين
        $Money = $num_order_for_course * $course->cost_course;

        // المبلغ الذي جمعه المعهد بعد إعطاء المدرس نسبته
        $Money_without_teacher = $Money * ($course->percent_teacher) / 100;

        //مصاريف الدورة الكلية
        $expenses = Expenses::where('course_id',$order->course_id)->sum('total_cost') ?? 0;

        //مربح المعهد من الدورة
        $Money_win =  $Money_without_teacher - $expenses ;

        if ($Money_win >= $course->Minimum_win) {
            $course->Course_status = 1;
            // $course->save();

            // if ($note_student->save()) {
            //     $user = User::find($student->user_id);  // استبدل بمعرف المستخدم المناسب
            //     $parentt = Parentt::find($student->parentt_id);
            // $message = 'This is a test notification!';
            // if ($user) {
            //     $user->notify(new MyNotification($message));
            // }
            
            // if ($parentt) {
            //     $parentt->notify(new MyNotification($message));
            // }
            // }
            //إرسال إشعارات
            if ($course->save()) {
                $users = User::where('user_type','admin')->get();
                $message = 'تم إكتمال العدد للدورة (' . $course->name_course . ') و تم فتحها';

                foreach ($users as $user) {
                    $user->notify(new MyNotification($message));
                }

                


            }
        }


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
        'amount' => 'required|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // التحقق من وجود الموظف في قاعدة البيانات
    $employee = Employee::find($idemployee);
    if (!$employee) {
        return response()->json(['message' => 'Employee not found'], 404);
    }

    $amount = $request->amount;
    $year = date('Y');
    $month = date('m');

    // حساب مجموع السلف التي أخذها الموظف هذا الشهر
    $totalSolfaThisMonth = $employee->maturitie()
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->sum('amount');

    // التحقق إذا كانت السلفة الجديدة تتجاوز الراتب
    if ($totalSolfaThisMonth + $amount > $employee->salary) {
        return response()->json(['message' => 'Total maturities for this month exceed the employee\'s salary'], 400);
    }

    // إضافة السلفة الجديدة
    $maturite = new Maturitie();
    $maturite->amount = $amount;
    $maturite->employee_id = $idemployee;
    $maturite->save();

    return response()->json(['success' => 'Maturitie added successfully for employee']);
}

    //عرض جميع المناقشات الخاصة بشعبة الطالب فقط عنوان و اسم المدرس
    public function display_post($section_id)
{
        $post = Post::where('section_id',$section_id)->with('subject')->with('teacher.user')->get();
        return $post;
}

public function all_course()
{
    $courses = Course::with('subject', 'classs', 'teacher.user')->whereHas('order', function ($query) {
        $query->where('student_type', 10);
    })
        ->withCount(['order as orders_with_student_type_10' => function ($query) {
            $query->where('student_type', 10);
        }])
        ->orderBy('orders_with_student_type_10', 'desc')->get();

    return $courses;
}

public function display_subject_for_class($class_id)
{
        $subject = Subject::where('class_id', $class_id)->get();

        return $subject;
}

    //البحث عن طالب ضمن كل طلاب العام الدراسي
    //مثال إذا دخلت زهراء الصوص و بالداتا عندي زهراء الصوص و زهراء محمد و محمد الصوص فالخرج هو جميع يلي ذكرتو بالداتا
//     public function search_student(Request $request)
//     {
//         $student = User::where('user_type', 'student')
//             ->where('status', '1')
//             ->where(function ($query) use ($request) {
//                 $query->where('first_name', 'LIKE', "%{$request->q}%")
//                       ->orWhere('last_name', 'LIKE', "%{$request->q}%");
//             })
//             ->get();

//         return $student;
// }

    //البحث عن طالب ضمن كل طلاب العام الدراسي
    //مثال إذا دخلت زهراء الصوص و بالداتا عندي زهراء الصوص و زهراء محمد و محمد الصوص فالخرج هو فقط زهراء الصوص و حتى لو مركب
public function search_student(Request $request)
{
    // تقسيم مدخل البحث إلى أجزاء بناءً على المسافة
    $keywords = explode(' ', $request->q);

    // إعداد استعلام أساسي
    $query = User::where('user_type', 'student')
                 ->where('status', '1');

    // إضافة شروط البحث لكل كلمة في الكلمات المفتاحية
    foreach ($keywords as $keyword) {
        $query->where(function ($subQuery) use ($keyword) {
            $subQuery->where('first_name', 'LIKE', "%{$keyword}%")
                     ->orWhere('last_name', 'LIKE', "%{$keyword}%");
        });
    }

    // تنفيذ الاستعلام
    $student = $query->with('student')->get();

    return response()->json($student);
}

    //البحث عن طالب ضمن طلاب شعبة محددة
    public function search_student_in_section(Request $request, $section_id)
{
    // تقسيم مدخل البحث إلى أجزاء بناءً على المسافة
    $keywords = explode(' ', $request->q);

    // إعداد استعلام أساسي
    $query = User::where('user_type', 'student')
                 ->where('status', '1')->whereHas('student', function ($query1) use ($section_id) {
                    $query1->whereHas('section', function($query2) use ($section_id) {
                        $query2->where('id', $section_id);
                    });
                 });

    // إضافة شروط البحث لكل كلمة في الكلمات المفتاحية
    foreach ($keywords as $keyword) {
        $query->where(function ($subQuery) use ($keyword) {
            $subQuery->where('first_name', 'LIKE', "%{$keyword}%")
                     ->orWhere('last_name', 'LIKE', "%{$keyword}%");
        });
    }

    // تنفيذ الاستعلام
    $student = $query->with('student')->get();

    return response()->json($student);
}

    //البحث عن أستاذ ضمن كل طلاب العام الدراسي
//     public function search_teacher(Request $request)
// {
//     // تقسيم مدخل البحث إلى أجزاء بناءً على المسافة
//     $keywords = explode(' ', $request->q);

//     // إعداد استعلام أساسي
//     $query = User::where('user_type', 'teacher')
//                  ->where('status', '1');

//     // إضافة شروط البحث لكل كلمة في الكلمات المفتاحية
//     foreach ($keywords as $keyword) {
//         $query->where(function ($subQuery) use ($keyword) {
//             $subQuery->where('first_name', 'LIKE', "%{$keyword}%")
//                      ->orWhere('last_name', 'LIKE', "%{$keyword}%");
//         });
//     }

//     // تنفيذ الاستعلام
//     $teacher = $query->with('teacher')->get();

//     return response()->json($teacher);
// }

public function search_teacher(Request $request)
{
    // تقسيم مدخل البحث إلى أجزاء بناءً على المسافة
    $keywords = explode(' ', $request->q);

    // إعداد استعلام أساسي لجلب المدرسين فقط
    $query = Teacher::whereHas('user', function($q) use ($keywords) {
        $q->where('status', '1'); // التأكد أن المستخدم نشط
        foreach ($keywords as $keyword) {
            $q->where(function ($subQuery) use ($keyword) {
                $subQuery->where('first_name', 'LIKE', "%{$keyword}%")
                         ->orWhere('last_name', 'LIKE', "%{$keyword}%");
            });
        }
    });

    // تنفيذ الاستعلام مع جلب معلومات المدرس ومعلومات المستخدم المرتبطة
    $teachers = $query->with('user')->get();

    return $teachers;
}



public function search_employee(Request $request)
{
    // تقسيم مدخل البحث إلى أجزاء بناءً على المسافة
    $keywords = explode(' ', $request->q);

    // إعداد استعلام أساسي
    $query = Employee::where('status', '1');

    // إضافة شروط البحث لكل كلمة في الكلمات المفتاحية
    foreach ($keywords as $keyword) {
        $query->where(function ($subQuery) use ($keyword) {
            $subQuery->where('first_name', 'LIKE', "%{$keyword}%")
                     ->orWhere('last_name', 'LIKE', "%{$keyword}%");
        });
    }

    // تنفيذ الاستعلام
    $employee = $query->get();

    return response()->json($employee);
}


     //zahraa_edit
    //معاشات المدرسين و الموظفيين بشكل أوتوماتيكي كل شهر
    // public function salary_all()
    // {
    //     $teachers = User::where('user_type', 'teacher')->where('status', '1')->get();
    //     $teacher_ids = [];

    //     foreach ($teachers as $t) {
    //         $teacher = $t->teacher; // جلب العلاقة teacher
    //         if ($teacher) {
    //             $teacher_ids[] = $teacher->id; // جلب الـ id من جدول teacher
    //         }
    //     }

    //     // return $teacher_ids; // إرجاع قائمة الـ ids
    // }
//     public function salary_all()
// {
//     $teachers = User::where('user_type', 'teacher')->where('status', '1')->get();
//     $teacher_ids = [];
//     $salaries = []; // مصفوفة لتخزين الرواتب

//     // استدعاء وحدة التحكم الأخرى
//     $salaryController = new AdminOperationController(); // استبدل OtherController باسم الوحدة الصحيحة

//     foreach ($teachers as $t) {
//         $teacher = $t->teacher; // جلب العلاقة teacher
//         if ($teacher) {
//             $teacher_ids[] = $teacher->id; // جلب الـ id من جدول teacher

//             // استدعاء الدالة لحساب الراتب
//             $year = date('Y'); // أو أي سنة تريدها
//             $month = date('m'); // أو أي شهر تريد
//             $salaryData = $salaryController->desplay_teacher_salary($teacher->id, $year, $month);

//             // إضافة بيانات الراتب إلى المصفوفة
//             $salaries[] = [
//                 // 'teacher_id' => $teacher->id,
//                 'salary_data' => $salaryData,
//             ];
//         }
//     }

//     return response()->json($salaries); // إرجاع قائمة الرواتب
// }

// public function salary_all()
// {
//     $teachers = User::where('user_type', 'teacher')->where('status', '1')->get();

//     // استدعاء وحدة التحكم الأخرى
//     $salaryController = new AdminOperationController(); // استبدل OtherController باسم الوحدة الصحيحة

//     foreach ($teachers as $t) {
//         $teacher = $t->teacher; // جلب العلاقة teacher
//         if ($teacher) {
//             // استدعاء الدالة لحساب الراتب
//             $year = date('Y'); // السنة الحالية أو يمكنك تخصيصها
//             $month = date('m'); // الشهر الحالي أو يمكنك تخصيصه
//             $salaryData = $salaryController->desplay_teacher_salary($teacher->id, $year, $month);

//             // تخزين بيانات الراتب في جدول teacher_salaries
//             DB::table('teacher_salaries')->insert([
//                 'salary_of_teacher' => $salaryData['total_salary'], // تأكد من وجود قيمة للراتب في الاستجابة
//                 'month' => date('Y-m-d', strtotime($year . '-' . $month . '-01')), // تحديد تاريخ الشهر
//                 'teacher_id' => $teacher->id, // ربط بالمعلم
//                 'employee_id' => null, // ربط بالموظف (المستخدم)
//                 'status' => 0, // حالة الراتب (لم يستلم الراتب)
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ]);
//         }
//     }

//     return response()->json(['message' => 'Salaries calculated and stored successfully']);
// }


   //zahraa_edit
// public function desplay_employee_salary($employee_id, $year, $month)
// {
//     $employees = Employee::where('id',$employee_id)->whereHas('maturitie')
// }


//zahraa_edit
public function desplay_employee_salary($employee_id)
{
    // استرجاع الموظف مع العلاقة المرتبطة maturitie
    $employee = Employee::where('id', $employee_id)
        ->with('maturitie') // جلب العلاقة maturitie
        ->first();

    // التأكد من أن الموظف موجود
    if (!$employee) {
        return response()->json(['error' => 'Employee not found'], 404);
    }

// استرجاع مجموع المبالغ للسلف لهذا الشهر
$totalSolfaThisMonth = $employee->maturitie()
->whereYear('created_at', $year)
->whereMonth('created_at', $month)
->sum('amount');
// حساب الراتب النهائي بعد خصم السلف لهذا الشهر
$finalSalary = $employee->salary - $totalSolfaThisMonth;
// التحقق من وجود بيانات السلف
if ($totalSolfaThisMonth === 0) {
return response()->json([
    'employee_id' => $employee->id,
    'original_salary' => $employee->salary,
    'message' => 'No maturities for this month',
    'final_salary' => $employee->salary, // إذا لم تكن هناك سلف، يبقى الراتب كما هو
]);
}
// إرجاع البيانات مع الراتب النهائي بعد خصم السلف
return response()->json([
'employee_id' => $employee->id,
'original_salary' => $employee->salary,
'total_maturities' => $totalSolfaThisMonth,
'final_salary' => $finalSalary,
]);
}
// public function e ($employee_id)
// {
//     $employee = Employee::where('id', $employee_id)->first();
//     //عدد المعاشات يلي استلمت أو تحدد مصيرها
//     $num_salary = Salary::where('employee_id', $employee_id)->count();

//     //معاشه السنوي للأشهر السابقة دون حذف السلف
//     $sum_salary_orginal = $employee->salary * $num_salary;
// }

public function salary_all()
{
    $teachers = User::where('user_type', 'teacher')->where('status', '1')->get();

    // استدعاء وحدة التحكم الأخرى
    $salaryController = new AdminOperationController(); // استبدل OtherController باسم الوحدة الصحيحة

    foreach ($teachers as $t) {
        $teacher = $t->teacher; // جلب العلاقة teacher
        if ($teacher) {
            // استدعاء الدالة لحساب الراتب
            $year = date('Y'); // السنة الحالية أو يمكنك تخصيصها
            $month = date('m'); // الشهر الحالي أو يمكنك تخصيصه
            $salaryResponse = $salaryController->desplay_teacher_salary($teacher->id, $year, $month);

            // تحويل الاستجابة إلى كائن أو مصفوفة
            $salaryData = $salaryResponse->getData(true); // تحويل الاستجابة إلى مصفوفة

            // التحقق من وجود بيانات الراتب
            if (isset($salaryData[2])) { // الوصول إلى الراتب (العنصر الثالث في المصفوفة التي يتم إرجاعها)
                // تخزين بيانات الراتب في جدول teacher_salaries
                DB::table('salary')->insert([
                    'salary_of_teacher' => $salaryData[2], // الراتب النهائي
                    'month' => date('Y-m-d', strtotime($year . '-' . $month . '-01')), // تحديد تاريخ الشهر
                    'teacher_id' => $teacher->id, // ربط بالمعلم
                    'employee_id' => null, // ربط بالموظف (المستخدم)
                    'status' => 0, // حالة الراتب (لم يستلم الراتب)
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // التعامل مع الحالة إذا لم توجد البيانات المطلوبة
                return response()->json(['error' => 'Salary data not found'], 404);
            }
        }
    }

    return response()->json(['message' => 'Salaries calculated and stored successfully']);
}

    //عرض جميع صفوف المعهد
public function display_all_class()
{
    $class = Classs::all();

    return $class;
}

    //عرض شعب مدرس حسب صف محدد
    public function display_section_for_class_teacher($class_id, $teacher_id)
    {
        // جلب الأقسام التي يدرسها المدرس
        $sections_teacher = Teacher_section::where('teacher_id', $teacher_id)
                                            // ->where('class_id', $class_id)
                                            ->get();

        $result = [];
        foreach ($sections_teacher as $section) {
            // جلب القسم المرتبط بالفصل وبالمدرس
            $section_class_teacher = Section::where('id', $section->section_id)
                                            ->where('class_id', $class_id)
                                            ->first();
            if ($section_class_teacher) {
                $result[] = $section_class_teacher;
            }
        }

        // التحقق إذا كانت النتيجة فارغة
        if (empty($result)) {
            return response()->json(["message" => "This teacher does not have sections in this class"], 404);
        }

        return response()->json($result);
    }


    //عرض طلاب سنة محددة
    public function desplay_all_student_regester_in_year($year)
    {
        $student = User::where('year',$year)->where('user_type', 'student')->with('student')->get();
        return response()->json([$student,'all student regester here']);
    }

    public function desplay_all_teacher_regester_in_year($year)
    {
        $student = User::where('year',$year)->where('user_type', 'teacher')->with('teacher')->get();
        return response()->json([$student,'all teacher regester here']);
    }

    public function class_s($s_id)
{
 $s=Section::where('id', $s_id)->with('classs')->first();
return $s;}

public function updateAbsence_for_student(Request $request, $student_id, $date)
{
    $absence = Out_Of_Work_Student::where('student_id', $student_id)->where('date',$date)->first();

    $validator = Validator::make($request->all(), [
        'justification' => 'string',
    ]);

    if ($validator->fails()) {
        return $this->responseError(['errors' => $validator->errors()]);
    }

    if ($request->has('justification') && !empty($request->justification)) {
        $absence->justification = $request->justification;
    }

    if ($absence->save()) {
        return response()->json(['message' => 'Absence justification updated successfully'], 200);
    }

    

}

public function all_teatcher()
{
    $academy = Academy::find(1);

    $teachers = Teacher::with('user')->whereHas('user', function ($query) use ($academy) {
        $query->where('year', $academy->year);
    })->get();

    return $teachers;
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
    // public function money_from_all_course(Request $request)
    //  {
    //     $query = Pay_Fee::query();

    //     // تصفية حسب اليوم
    //     if ($request->has('day') && !empty($request->day)) {
    //         $query->whereDay('date', $request->day);
    //     }

    //     // تصفية حسب الشهر
    //     if ($request->has('month') && !empty($request->month)) {
    //         $query->whereMonth('date', $request->month);
    //     }

    //     // تصفية حسب السنة
    //     if ($request->has('year') && !empty($request->year)) {
    //         $query->whereYear('date', $request->year);
    //     }

    //     // تصفية حسب السنة الدراسية
    //     if ($request->has('year_studey') && !empty($request->year_studey)) {
    //         // جلب المستخدمين الذين يطابقون السنة الدراسية المحددة والنوع "طالب"
    //         $course = Course::where('year', $request->year_studey)->get();

    //         // التأكد من جلب الدورة بشكل صحيح
    //         if ($course->isEmpty()) {
    //             return response()->json(['message' => 'No Courses found'], 404);
    //         }

    //         // جلب المعرفات فقط
    //         $courseIds = $course->pluck('id');

    //         // إضافة شرط السنة الدراسية إلى الاستعلام
    //         $query->whereIn('course_id', $courseIds);

    //     }

    //     // if ($request->has('subject_name') && !empty($request->subject_name)) {

    //     //     $subject = Subject::where('name',$request->subject_name)

    //     //     $course = Course::where('subject_id', $request->subject)->get();

    //     //     // التأكد من جلب الدورة بشكل صحيح
    //     //     if ($course->isEmpty()) {
    //     //         return response()->json(['message' => 'No Courses found'], 404);
    //     //     }

    //     //     // جلب المعرفات فقط
    //     //     $courseIds = $course->pluck('id');

    //     //     // إضافة شرط السنة الدراسية إلى الاستعلام
    //     //     $query->whereIn('course_id', $courseIds);

    //     // }



    //     $query->whereNotNull('course_id');
    //     $pays = $query->get();

    //     // حساب المجموع
    //     $total_amount = $pays->sum('amount_money');

    //     return response()->json([
    //         'total_amount' => $total_amount,
    //         'pays' => $pays,
    //     ]);

    //  }



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
            $course = Course::where('year', $request->year_studey)->get();
    
            if ($course->isEmpty()) {
                return response()->json(['message' => 'No Courses found'], 404);
            }
    
            // جلب معرفات الدورات
            $courseIds = $course->pluck('id');
            $query->whereIn('course_id', $courseIds);
        }
    
        // تأكد من جلب الدفعات المرتبطة بالدورات فقط
        $query->whereNotNull('course_id');
        $pays = $query->get();
    
        // حساب المجموع بعد طرح نسبة المدرس
        $total_amount = 0;
    
        foreach ($pays as $pay) {
            $course = Course::find($pay->course_id);
    
            if ($course) {
                // نسبة المدرسة من المبلغ (100% - نسبة المدرس)
                $school_percent = 100 - $course->percent_teacher;
    
                // حساب المبلغ الذي تحصل عليه المدرسة
                $school_amount = ($pay->amount_money * $school_percent) / 100;
    
                // إضافة المبلغ إلى المجموع
                $total_amount += $school_amount;
            }
        }
    
        return response()->json([
            'total_amount' => $total_amount,
            'pays' => $pays,
        ]);
    }

//المصاريف دون فلترة
//      public function all_expenses(Request $request)
// {
//     // استرداد جميع النفقات
//     $expenses = Expenses::all();

//     // جمع التكلفة الإجمالية للنفقات
//     $sum_expenses = $expenses->sum('total_cost');

//     return $sum_expenses;
// }

    //المبلغ الذي دفعه المعهد مصاريف حسب يوم أو شهر أو سنة أو عام دراسي أو دمج بيناتون
    public function all_expenses(Request $request)
{
    // إنشاء الاستعلام الأساسي
    $query = Expenses::query();

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
        // إضافة شرط السنة الدراسية إلى الاستعلام
        $query->where('year', $request->year_studey);
    }

    // الحصول على قائمة النفقات
    $expenses = $query->get();

    // حساب مجموع التكلفة الإجمالية للنفقات
    $sum_expenses = $expenses->sum('total_cost');

    // إرجاع النتيجة بصيغة JSON
    return response()->json([
        'total_expenses' => $sum_expenses,
        'expenses' => $expenses,
    ]);
}

public function all_Maturitie(Request $request)
{
    // إنشاء الاستعلام الأساسي
    $query = Maturitie::query();

    // تصفية حسب اليوم
    if ($request->has('day') && !empty($request->day)) {
        $query->whereDay('created_at', $request->day);
    }

    // تصفية حسب الشهر
    if ($request->has('month') && !empty($request->month)) {
        $query->whereMonth('created_at', $request->month);
    }

    // تصفية حسب السنة
    if ($request->has('year') && !empty($request->year)) {
        $query->whereYear('created_at', $request->year);
    }

    // // تصفية حسب السنة الدراسية
    // if ($request->has('year_studey') && !empty($request->year_studey)) {
    //     // إضافة شرط السنة الدراسية إلى الاستعلام
    //     $query->where('year', $request->year_studey);
    // }

    // الحصول على قائمة النفقات
    $maturities = $query->get();

    // حساب مجموع التكلفة الإجمالية للنفقات
    $sum_maturities = $maturities->sum('amount');

    // إرجاع النتيجة بصيغة JSON
    return response()->json([
        'total_maturities' => $sum_maturities,
        'maturities' => $maturities,
    ]);
}

public function all_salary(Request $request)
{
    // إنشاء الاستعلام الأساسي
    $query = Salary::query();

    // تصفية حسب اليوم
    if ($request->has('day') && !empty($request->day)) {
        $query->whereDay('created_at', $request->day);
    }

    // تصفية حسب الشهر
    if ($request->has('month') && !empty($request->month)) {
        $query->whereMonth('created_at', $request->month);
    }

    // تصفية حسب السنة
    if ($request->has('year') && !empty($request->year)) {
        $query->whereYear('created_at', $request->year);
    }

    // تصفية حسب السنة الدراسية
    if ($request->has('year_studey') && !empty($request->year_studey)) {
        $query->where('year', $request->year_studey);
    }

    // الحصول على قائمة الرواتب
    $salary = $query->get();

    // حساب مجموع الرواتب
    $sum_salary = $salary->sum('salary_of_teacher');

    // إرجاع النتيجة بصيغة JSON
    return response()->json([
        'total_salary' => $sum_salary,
        'salary' => $salary,
    ]);
}

public function calculate_balance(Request $request)
{
    // 1. الحصول على المدفوعات من الأقساط
    $query_fee = Pay_Fee::query();

    // تصفية حسب اليوم
    if ($request->has('day') && !empty($request->day)) {
        $query_fee->whereDay('date', $request->day);
    }

    // تصفية حسب الشهر
    if ($request->has('month') && !empty($request->month)) {
        $query_fee->whereMonth('date', $request->month);
    }

    // تصفية حسب السنة
    if ($request->has('year') && !empty($request->year)) {
        $query_fee->whereYear('date', $request->year);
    }

    // تصفية حسب السنة الدراسية
    if ($request->has('year_studey') && !empty($request->year_studey)) {
        $students = User::where('year', $request->year_studey)
                        ->where('user_type', 'student')
                        ->with('student:id,user_id')
                        ->get();

        if ($students->isNotEmpty()) {
            $studentIds = $students->pluck('student.id')->toArray();
            $query_fee->whereIn('student_id', $studentIds);
        }
    }

    // تصفية المدفوعات التي ليس لها course_id
    $query_fee->whereNull('course_id');
    $pays_fee = $query_fee->get();
    $total_fee = $pays_fee->sum('amount_money');

    // 2. الحصول على المدفوعات من الدورات
    $query_course = Pay_Fee::query();

    // نفس الفلاتر السابقة (اليوم، الشهر، السنة، السنة الدراسية)
    if ($request->has('day') && !empty($request->day)) {
        $query_course->whereDay('date', $request->day);
    }

    if ($request->has('month') && !empty($request->month)) {
        $query_course->whereMonth('date', $request->month);
    }

    if ($request->has('year') && !empty($request->year)) {
        $query_course->whereYear('date', $request->year);
    }

    if ($request->has('year_studey') && !empty($request->year_studey)) {
        $courses = Course::where('year', $request->year_studey)->get();

        if ($courses->isNotEmpty()) {
            $courseIds = $courses->pluck('id')->toArray();
            $query_course->whereIn('course_id', $courseIds);
        }
    }

    $query_course->whereNotNull('course_id');
    $pays_course = $query_course->get();

    // تعديل حساب المجموع بعد طرح نسبة المدرس
    $total_course = 0;
    foreach ($pays_course as $pay) {
        $course = Course::find($pay->course_id);

        if ($course) {
            // حساب نسبة المدرسة
            $school_percent = 100 - $course->percent_teacher;

            // حساب المبلغ الصافي الذي تحصل عليه المدرسة
            $school_amount = ($pay->amount_money * $school_percent) / 100;

            // إضافة المبلغ إلى المجموع
            $total_course += $school_amount;
        }
    }

    // 3. حساب مجموع النفقات
    $query_expenses = Expenses::query();

    if ($request->has('day') && !empty($request->day)) {
        $query_expenses->whereDay('date', $request->day);
    }

    if ($request->has('month') && !empty($request->month)) {
        $query_expenses->whereMonth('date', $request->month);
    }

    if ($request->has('year') && !empty($request->year)) {
        $query_expenses->whereYear('date', $request->year);
    }

    if ($request->has('year_studey') && !empty($request->year_studey)) {
        $query_expenses->where('year', $request->year_studey);
    }

    $expenses = $query_expenses->get();
    $total_expenses = $expenses->sum('total_cost');

    // 4. حساب مجموع الرواتب
    $query_salary = Salary::query();

    if ($request->has('day') && !empty($request->day)) {
        $query_salary->whereDay('created_at', $request->day);
    }

    if ($request->has('month') && !empty($request->month)) {
        $query_salary->whereMonth('created_at', $request->month);
    }

    if ($request->has('year') && !empty($request->year)) {
        $query_salary->whereYear('created_at', $request->year);
    }

    if ($request->has('year_studey') && !empty($request->year_studey)) {
        $query_salary->where('year', $request->year_studey);
    }

    $salaries = $query_salary->get();
    $total_salary = $salaries->sum('salary_of_teacher');

    // 5. الحصول على مجموع الكلف من جدول 'breake' و 'bus'
    $total_break_cost = 0;
    $total_bus_cost = 0;

    if ($request->has('year') && !empty($request->year)) {
        $total_break_cost += Breake::whereYear('created_at', $request->year)
                                   ->sum('cost_from_breake');
        $total_bus_cost += Bus::whereYear('created_at', $request->year)
                                   ->sum('cost_from_bus');
    }

    if ($request->has('year_studey') && !empty($request->year_studey)) {
        $total_break_cost += Breake::where('year', $request->year_studey)
                                   ->sum('cost_from_breake');
        $total_bus_cost += Bus::where('year', $request->year_studey)
                                   ->sum('cost_from_bus');
    }

    // 6. حساب الربح أو الخسارة
    $total_income = $total_fee + $total_course + $total_break_cost + $total_bus_cost;
    $total_expenses_salary = $total_expenses + $total_salary;
    $balance = $total_income - $total_expenses_salary;

    // إرجاع النتيجة بصيغة JSON
    return response()->json([
        'total_income' => $total_income,
        'total_expenses_salary' => $total_expenses_salary,
        'balance' => $balance
    ]);
}


// public function all_salary_employees_teacher(Request $request)
// {
// //    //استرداد جميع النفقات
// //     $salary = Employee::all();

// //     // جمع التكلفة الإجمالية للنفقات
// //     $sum_salary = $salary->sum('salary');

// //     return response()->json([
// //         'total_salary_employee' => $sum_salary,
// //         'employee' => $salary,
// //     ]);

// $employees = Employee::where('status', '1')->whereHas('salary', sum('salary_of_teacher'))->first();
// return $employees;

// }

// public
public function all_salary_employees_teacher(Request $request)
{
    // استرداد جميع الموظفين الذين لهم رواتب وحالتهم نشطة
    $employees = Employee::where('status', '1')
        ->with('salary') // جلب العلاقة salary
        ->get();

    // جمع التكلفة الإجمالية لرواتب الموظفين
    $sum_salary = $employees->sum(function($employee) {
        return $employee->salary->sum('salary_of_teacher');
    });

    return response()->json([
        'total_salary_employee' => $sum_salary,
        'employees' => $employees,
    ]);
}



   //zahraa_edit
   public function all_money_from_breake_bus()
   {
    // إنشاء الاستعلام الأساسي
    $query = Breake::query();

    // تصفية حسب اليوم
    // if ($request->has('day') && !empty($request->day)) {
    //     $query->whereDay('date', $request->day);
    // }

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
        // إضافة شرط السنة الدراسية إلى الاستعلام
        $query->where('year', $request->year_studey);
    }

    // الحصول على قائمة النفقات
    $expenses = $query->get();

    // حساب مجموع التكلفة الإجمالية للنفقات
    $sum_expenses = $expenses->sum('total_cost');

    // إرجاع النتيجة بصيغة JSON
    return response()->json([
        'total_expenses' => $sum_expenses,
        'expenses' => $expenses,
    ]);

   }




    //مجموع دفعات أقساط الطلاب///////////////////////////////////////
    //مجموع دفعات الدورات//////////////////////////
    //مجموع البوفيه
    //مجموع النقل



    //معاشات الأساتذة
    //معاشات الموظفين
    //سلف///////////////////////////////////
    //مصاريف///////////////////////////

//     public function register_student1(Request $request)
// {
//     $academy = Academy::find(1);
//     /***حساب الطالب***/
//     $validator3 = Validator::make($request->all(), [
//         'first_name_s' => 'required',
//         'last_name_s' => 'required|string',
//         'father_name' => 'required|string',
//         'mother_name' => 'required|string',
//         'birthday' => 'required|date',
//         'gender'=>'required',
//         'phone_s' => 'required',
//         'address_s' => 'required',
//         'email'=>'required|email',
//         'password_s' => 'required|min:8',
//         'conf_password_s' => 'required|min:8',
//     ]);

//     if ($validator3->fails()) {
//         return $this->responseError(['errors' => $validator3->errors()]);
//     }

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
//         'first_name'=>'required|string',
//         'last_name' => 'required|string',
//         'phone' => 'required',
//         'address' => 'required',
//         'email'=>'required|email',
//         'password' => 'required|min:8',
//         'conf_password' => 'required|min:8',
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
//     $parentt->year = $academy->year;
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

// // شغال و كامل و رابطة روان
// public function register(Request $request)
// {
//     $academy = Academy::find(1);

//     // Validate student data
//     $validatorStudent = Validator::make($request->all(), [
//         'first_name_s' => 'required',
//         'last_name_s' => 'required|string',
//         'father_name' => 'required|string',
//         'mother_name' => 'required|string',
//         'birthday' => 'required|date',
//         'gender' => 'required',
//         // 'phone_s' => ['required', 'regex:/^\+963\s?9[0-9]{1}\s?[0-9]{3}\s?[0-9]{3}\s?[0-9]{3}$/'],
//         'address_s' => 'required',
//         'email' => 'required|email|unique:users',
//         'password_s' => 'required|min:8',
//         'conf_password_s' => 'required|min:8|same:password_s',
//         'school_tuition' => 'required',
//         'class_id' => 'required',
//         'name_section' => 'required|string|exists:sections,num_section',
//         // 'student_type' => 'required',
//         // 'calssification' => 'required_if:student_type,0|in:0,1',
//     ]);

//     // Validate parent data
//     $validatorParent = Validator::make($request->all(), [
//         'first_name_p' => 'required|string',
//         'last_name_p' => 'required|string',
//         // 'phone_p' => ['required', 'regex:/^\+963\s?9[0-9]{1}\s?[0-9]{3}\s?[0-9]{3}\s?[0-9]{3}$/'],
//         'address_p' => 'required',
//         'email_p' => 'required|email',
//         'password_p' => 'required|min:8',
//         'conf_password_p' => 'required|min:8|same:password_p',
//     ]);

//     // Check if any validation errors occurred
//     if ($validatorStudent->fails() || $validatorParent->fails()) {
//         $errors = $validatorStudent->errors()->merge($validatorParent->errors());
//         return $this->responseError(['errors' => $errors]);
//     }

//     // Create parent record
//     $parentt = Parentt::where('email', $request->email_p)->first();
//     if (!$parentt) {
//         $parentt = new Parentt();
//         $parentt->first_name = $request->first_name_p;
//         $parentt->last_name = $request->last_name_p;
//         if ($request->has('phone_p') && !empty($request->phone_p)) {
//             $phone = $request->phone_p;
//             if (!preg_match('/^(\+?963|0)?9\d{8}$/', $phone)) {
//                 return response()->json(['status' => 'error', 'message' => 'Invalid Syrian phone number'], 400);
//             }
//             $parentt->phone = $request->phone_p;
//         }
//         // $parentt->phone = $request->phone_p;

//         $parentt->address = $request->address_p;
//         $parentt->email = $request->email_p;
//         $parentt->year = $academy->year;
//         $parentt->password = Hash::make($request->password_p);
//         $parentt->conf_password = Hash::make($request->conf_password_p);
//     }

//     // Create student record
//     $user = new User();
//     $user->first_name = $request->first_name_s;
//     $user->last_name = $request->last_name_s;
//     $user->father_name = $request->father_name;
//     $user->mother_name = $request->mother_name;
//     $user->birthday = $request->birthday;
//     $user->gender = $request->gender;
//     if ($request->has('phone_s') && !empty($request->phone_s)) {
//         $phone = $request->phone_s;
//         if (!preg_match('/^(\+?963|0)?9\d{8}$/', $phone)) {
//             return response()->json(['status' => 'error', 'message' => 'Invalid Syrian phone number'], 400);
//         }
//         $user->phone = $request->phone_s;
//     }
//     // $user->phone = $request->phone_s;
//     $user->address = $request->address_s;
//     $user->year = $academy->year;
//     $user->email = $request->email;
//     $user->password = Hash::make($request->password_s);
//     $user->conf_password = Hash::make($request->conf_password_s);
//     $user->user_type = 'student';

//     // Create student profile
//     $student = new Student();
//     $student->school_tuition = $request->school_tuition;
//     $student->class_id = $request->class_id;

//     // تحديد الشعبة للطالب
//     $section = Section::where('num_section', $request->name_section)->where('class_id', $student->class_id)->first();
//     if (!$section) {
//         return response()->json([
//             'status' => 'false',
//             'message' => 'Subject not found for the specified class_id',
//         ]);
//     }
//     $student->section_id = $section->id;

//     // $student->student_type = $request->student_type;
//     // if ($request->student_type != 0 && empty($request->calssification)) {
//     //     $student->calssification = 2;
//     // }
//     // else {
//     //     $student->calssification = $request->calssification;
//     // }
//     // $student->calssification = $request->student_type == 0 ? $request->calssification : null;


//     $parentt->save();
//     $user->save();
//     $student->user_id = $user->id;
//     $student->parentt_id = $parentt->id;
//     $student->save();

//     // Return response with created user, student, and parentt
//     return response()->json(['user' => $user, 'student' => $student, 'parentt' => $parentt]);
// }

// // رابطة روان عليه
// public function register(Request $request)
// {
//     $academy = Academy::find(1);

//     // Validate student data
//     $validatorStudent = Validator::make($request->all(), [
//         'first_name_s' => 'required',
//         'last_name_s' => 'required|string',
//         'father_name' => 'required|string',
//         'mother_name' => 'required|string',
//         'birthday' => 'required|date',
//         'gender' => 'required',
//         // 'phone_s' => ['required', 'regex:/^\+963\s?9[0-9]{1}\s?[0-9]{3}\s?[0-9]{3}\s?[0-9]{3}$/'],
//         'address_s' => 'required',
//         'email' => 'required|email|unique:users',
//         'password_s' => 'required|min:8',
//         'conf_password_s' => 'required|min:8|same:password_s',
//         // 'school_tuition' => 'required',
//         'class_id' => 'required',
//         'name_section' => 'required|string|exists:sections,num_section',
//         // 'student_type' => 'required',
//         // 'calssification' => 'required_if:student_type,0|in:0,1',
//     ]);

//     // Validate parent data
//     $validatorParent = Validator::make($request->all(), [
//         'first_name_p' => 'required|string',
//         'last_name_p' => 'required|string',
//         // 'phone_p' => ['required', 'regex:/^\+963\s?9[0-9]{1}\s?[0-9]{3}\s?[0-9]{3}\s?[0-9]{3}$/'],
//         'address_p' => 'required',
//         'email_p' => 'required|email',
//         'password_p' => 'required|min:8',
//         'conf_password_p' => 'required|min:8|same:password_p',
//     ]);

//     // Check if any validation errors occurred
//     if ($validatorStudent->fails() || $validatorParent->fails()) {
//         $errors = $validatorStudent->errors()->merge($validatorParent->errors());
//         return $this->responseError(['errors' => $errors]);
//     }

//     // Create parent record
//     $parentt = Parentt::where('email', $request->email_p)->first();
//     if (!$parentt) {
//         $parentt = new Parentt();
//         $parentt->first_name = $request->first_name_p;
//         $parentt->last_name = $request->last_name_p;
//         if ($request->has('phone_p') && !empty($request->phone_p)) {
//             $phone = $request->phone_p;
//             if (!preg_match('/^(\+?963|0)?9\d{8}$/', $phone)) {
//                 return response()->json(['status' => 'error', 'message' => 'Invalid Syrian phone number'], 400);
//             }
//             $parentt->phone = $request->phone_p;
//         }
//         // $parentt->phone = $request->phone_p;

//         $parentt->address = $request->address_p;
//         $parentt->email = $request->email_p;
//         $parentt->year = $academy->year;
//         $parentt->password = Hash::make($request->password_p);
//         $parentt->conf_password = Hash::make($request->conf_password_p);
//     }

//     // Create student record
//     $user = new User();
//     $user->first_name = $request->first_name_s;
//     $user->last_name = $request->last_name_s;
//     $user->father_name = $request->father_name;
//     $user->mother_name = $request->mother_name;
//     $user->birthday = $request->birthday;
//     $user->gender = $request->gender;
//     if ($request->has('phone_s') && !empty($request->phone_s)) {
//         $phone = $request->phone_s;
//         if (!preg_match('/^(\+?963|0)?9\d{8}$/', $phone)) {
//             return response()->json(['status' => 'error', 'message' => 'Invalid Syrian phone number'], 400);
//         }
//         $user->phone = $request->phone_s;
//     }
//     // $user->phone = $request->phone_s;
//     $user->address = $request->address_s;
//     $user->year = $academy->year;
//     $user->email = $request->email;
//     $user->password = Hash::make($request->password_s);
//     $user->conf_password = Hash::make($request->conf_password_s);
//     $user->user_type = 'student';

//     // Create student profile
//     $student = new Student();
//     $student->class_id = $request->class_id;

//     // تحديد الشعبة للطالب
//     $section = Section::where('num_section', $request->name_section)->where('class_id', $student->class_id)->first();
//     if (!$section) {
//         return response()->json([
//             'status' => 'false',
//             'message' => 'Section not found for the specified class_id',
//         ]);
//     }
//     $student->section_id = $section->id;

//     //تحديد قسط الطالب بحيث إذا الو أخ أو ابن مدرس أو ابن شهيد سيكون له حسم و ممكن حسم كامل
//     $fee_class = Fee_School::where('class_id',$student->class_id)->where('year',$academy->year)->value('amount');
//     if (!$fee_class) {
//         return 'You did not specify the annual premium for this class';
//     }
//     // return $fee_class;
//     $student->student_type = $request->student_type;
//     //قيم ال student_type
//     //0 قسط الصف دون حسم
//     //1 ابن شهيد
//     //2 ابن معلم
//     //3 حسم أشقاء
//     //4 حسم كامل


//     $son = Student::where('parentt_id',$parentt->id)->get();

//     if ($student->student_type == '1') {
// // return '1';
//         $student->school_tuition = $fee_class * (100 - $academy->resolve_martyr) / 100 ;
//     }

//     elseif ($student->student_type == '2') {
//         $student->school_tuition = $fee_class * (100 - $academy->resolve_Son_teacher) / 100 ;
//     }

//     elseif ($student->student_type == '4') {
//         $student->school_tuition = 0;
//     }
//     elseif ((count($son) != 0 && $request->has('student_type') && empty($request->student_type)) || $student->student_type == '3') {
//         foreach ($son as $s) {
//             $user_year = User::where('id',$s->user_id)->first();
//             if ($user_year->year == $academy->year) {
//                 $student->student_type = 3;
//                 $student->school_tuition = $fee_class * (100 - $academy->resolve_brother) / 100 ;
//                 break;
//             }

//             }
//     }

//     // //نبحث إن كان له أخوة و لنفس السنة لنعمل له حسم
//     // // $son = $parentt->student;
//     // $son = Student::where('parentt_id',$parentt->id)->get();
//     // // return count($son);
//     // if (count($son) != 0 && $request->has('student_type') && empty($request->student_type)) {
//     //     foreach ($son as $s) {
//     //     $user_year = User::where('id',$s->user_id)->first();
//     //     if ($user_year->year == $academy->year) {
//     //         $student->student_type = 3;
//     //         $student->school_tuition = $fee_class * (100 - $academy->resolve_brother) / 100 ;
//     //         break;
//     //     }

//     //     }
//     // }

//     else {
//         $student->school_tuition = $fee_class;

//     }


//     // $student->student_type = $request->student_type;
//     // if ($request->student_type != 0 && empty($request->calssification)) {
//     //     $student->calssification = 2;
//     // }
//     // else {
//     //     $student->calssification = $request->calssification;
//     // }
//     // $student->calssification = $request->student_type == 0 ? $request->calssification : null;


//     $parentt->save();
//     $user->save();
//     $student->user_id = $user->id;
//     $student->parentt_id = $parentt->id;
//     $student->save();

//     // Return response with created user, student, and parentt
//     return response()->json(['user' => $user, 'student' => $student, 'parentt' => $parentt]);
// }


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
        'address_s' => 'required',
        'email' => 'required|email|unique:users',
        'password_s' => 'required|min:8',
        'conf_password_s' => 'required|min:8|same:password_s',
        'class_id' => 'required',
        'name_section' => 'required|string|exists:sections,num_section',
    ]);

    // Validate parent data
    $validatorParent = Validator::make($request->all(), [
        'first_name_p' => 'required|string',
        'last_name_p' => 'required|string',
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

        $parentt->address = $request->address_p;
        $parentt->email = $request->email_p;
        $parentt->year = $academy->year;
        $parentt->password = Hash::make($request->password_p);
        $parentt->conf_password = Hash::make($request->conf_password_p);
    }
    elseif ($parentt->status == '0') {
        $parentt->status = '1';
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
    $user->address = $request->address_s;
    $user->year = $academy->year;
    $user->email = $request->email;
    $user->password = Hash::make($request->password_s);
    $user->conf_password = Hash::make($request->conf_password_s);
    $user->user_type = 'student';

    // Create student profile
    $student = new Student();
    $student->class_id = $request->class_id;

    // تحديد الشعبة للطالب
    $section = Section::where('num_section', $request->name_section)->where('class_id', $student->class_id)->first();
    if (!$section) {
        return response()->json([
            'status' => 'false',
            'message' => 'Section not found for the specified class_id',
        ]);
    }
    $student->section_id = $section->id;

    //تحديد قسط الطالب بحيث إذا الو أخ أو ابن مدرس أو ابن شهيد سيكون له حسم و ممكن حسم كامل
    $fee_class = Fee_School::where('class_id',$student->class_id)->where('year',$academy->year)->value('amount');
    if (!$fee_class) {
        return 'You did not specify the annual premium for this class';
    }
    $student->student_type = $request->student_type;
    //قيم ال student_type
    //0 قسط الصف دون حسم
    //1 ابن شهيد
    //2 ابن معلم
    //3 حسم أشقاء
    //4 حسم كامل

    $son = Student::where('parentt_id', $parentt->id)->whereHas('user', function ($query) {
        $query->where('status', '1');
    })->get();


    if ($student->student_type == '1') {
        $student->school_tuition = $fee_class * (100 - $academy->resolve_martyr) / 100 ;
    }

    elseif ($student->student_type == '2') {
        $student->school_tuition = $fee_class * (100 - $academy->resolve_Son_teacher) / 100 ;
    }

    elseif ($student->student_type == '4') {
        $student->school_tuition = 0;
    }
    elseif ((count($son) != 0 && $request->has('student_type') && empty($request->student_type)) || $student->student_type == '3') {
        foreach ($son as $s) {
            $user_year = User::where('id',$s->user_id)->first();
            if ($user_year->year == $academy->year) {
                $student->student_type = 3;
                $student->school_tuition = $fee_class * (100 - $academy->resolve_brother) / 100 ;
                break;
            }

            }
    }
    else {
        $student->school_tuition = $fee_class;
    }

    // تأكد من تحديد قيمة school_tuition قبل الحفظ
    if (is_null($student->school_tuition)) {
        $student->school_tuition = $fee_class; // تعيين قيمة افتراضية في حال لم يتم تعيينها
    }

    $parentt->save();
    $user->save();
    $student->user_id = $user->id;
    $student->parentt_id = $parentt->id;
    $student->save();

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


// public function showUserActivities()
// {
//     // تحقق من أن المستخدم الحالي هو admin
//     if (auth()->user()->user_type === 'admin') {
//         // جلب جميع السجلات من قاعدة البيانات مع معلومات المستخدمين
//         $logs = ActionLog::with('user')->latest()->get();

//         // تمرير السجلات إلى العرض
//         return view('admin.activities', compact('logs'));
//     }

//     // إذا لم يكن المستخدم admin، عرض صفحة خطأ
//     return abort(403, 'Access Denied');
// }

public function showUserActivities()
{
    // تحقق من أن المستخدم الحالي هو admin
    if (auth()->user()->user_type === 'admin') {
        // جلب جميع السجلات مع معلومات المستخدمين المرتبطة
        $logs = Actions_log::with('user')->latest()->get();

        // إعادة السجلات بصيغة JSON
        return response()->json([
            'success' => true,
            'data' => $logs,
        ], 200);
    }

    // إذا لم يكن المستخدم admin، إعادة رسالة خطأ بصيغة JSON
    return response()->json([
        'success' => false,
        'message' => 'Access Denied'
    ], 403);
}


public function all_action_for_user($user_id)
{
    $user = User::find($user_id); // جلب المستخدم برقم ID 1
    $logs = $user->actionLogs; // جلب كل الأنشطة التي قام بها هذا المستخدم

return $logs;

}

//$logs = auth()->user()->actionLogs; // جلب الأنشطة للمستخدم الحالي






}
