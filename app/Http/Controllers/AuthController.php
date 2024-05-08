<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Classs;
use App\Models\Order;
use App\Models\Parentt;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register_student(Request $request,$order_id)
    {

        $order = Order::find($order_id)->where('id',$order_id)->get;
        $validator = Validator::make($request->all(),[
            'mother_name' => 'required|string',
           // 'email'=>'required|email',
            //'password' => 'required|min:8',
            //'conf_password' => 'required|min:8',
            'user_type' => 'required|default:student',


        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $email = $order->first_name.Str::random(5)."@gmail.com";
        $password = $order->first_name.Str::random(6);
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
            return response()->json(['errors' => $validator1->errors()]);
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
            return response()->json(['errors' => $validator->errors()]);
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

        return response()->json([auth()->user(),'this is user profile']);

    }

    public function get_teacher_profile($teacher_id)
    {
        $user = User::find($teacher_id);
        if(!$user){
            return response()->json(['user not found ']);
        }
        $teacher = Teacher::where( 'user_id',$user->id );
        if(!$teacher)
        {
            return response()->json(['this user not teacher']);
        }
        return response()->json([$user,$teacher,'this is our teacher']);
    }


    public function update_teacher_profile(Request $request,$teacher_id)
    {
        $user = User::find($teacher_id);
        if(!$user){
            return response()->json(['user not found ']);
        }
        $teacher = Teacher::where( 'user_id',$user->id );
        if(!$teacher)
        {
            return response()->json(['this user not teacher']);
        }

        $validator = validator::make($request->all(),[
            'num_hour'=>'required',
            'cost_hour'=>'required',
            'num_our_added'=>'required',
            'note_hour_added'=>'required',
        ]);
        if($validator->fails())
        {
            return response()->json(['Please validate error',$validator->errors()]);
        }
        $teacher = Teacher::where( 'user_id',$user->id );
        $teacher->update([
            'num_hour'=>$request->num_hour,
            'cost_hour'=>$request->cost_hour,
            'num_our_added'=>$request->num_hour_added,
            'note_hour_added'=>$request->note_hour_added,
        ]);

    }

    public function update_profile_user(Request $request ,$id)
{

    $validator = Validator::make($request->all(),[
    'first_name'=>'required',
    'last_name'=>'required',
    'father_name'=>'required',
    'mother_name'=>'required',
    'birthday'=>'required',
    'phone'=>'required',
    'address'=>'required',
    'image'=>'required',

    ]);
    if($validator->fails())
    {
        return response()->json(['Please validate error',$validator->errors()]);
    }
    $user =User::where('id',$id)->first();

    $user->update([
    'first_name'=>$request->first_name,
    'last_name'=>$request->last_name,
    'father_name'=>$request->father_name,
    'mother_name'=>$request->mother_name,
    'birthday'=>$request->birthday,
    'phone'=>$request->phone,
    'address'=>$request->address,
    'image'=>$request->image,

]);


    return response()->json([$user,'the Estate created succeflly']);

}



}
