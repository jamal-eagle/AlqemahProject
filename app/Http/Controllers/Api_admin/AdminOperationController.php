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
use App\Models\Student;
use App\Models\Parentt;
use Illuminate\Support\Str;

class AdminOperationController extends BaseController
{

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        // check email
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
            return $this->responseError(['please  check your Auth','auth error']);
        }
    }

    public function logout(Request $request)
    {
        if(Auth::check()){
        $request->user()->currentAccessToken()->delete();
        return $this->responseError(['the user logged out']);
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
        $stud =Student::where('classifaction' ,'=', 1)->get();
        $student = User::where($stud->user_id, 'id')->get()->all();
            return response()->json([$stud,$student]);
    }
    else {
        $stud =Student::where('classifaction' ,'=', 0)->get();
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

}
