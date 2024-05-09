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
use Illuminate\Support\Str;
use App\Models\Note;
use App\Models\Note_Student;
use App\Models\Publish;

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

    public function student_classification($classifiaction)
    {
        if($classifiaction = 1){
        $stud =Student::where('classifiaction' ,'=', 1)->get();
        $student = User::where($stud->user_id, 'id')->get()->all();
            return response()->json([$stud,$student]);
    }
    else {
        $stud =Student::where('classifiaction' ,'=', 0)->get();
        $student = User::where($stud->user_id, 'id')->get()->all();
            return response()->json([$stud,$student]);
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

public function desplay_employee()
{
    $employee = Employee::get()->all();
    if(!$employee)
    {
        return response()->json(['you havenot any employee']);
    }

    return response()->json([$employee,'you havenot any employee']);
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


}
