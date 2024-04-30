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
            "date" => "required|date|after:today"
        ]);

        $new = new Appointment;

        $new->date =$request->date;
        $new->order_id = $order_id;

        $new->save();
    }

}
