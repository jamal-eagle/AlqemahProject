<?php

namespace App\Http\Controllers\Api_out_user;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
//use App\Models\User;
//use App\Models\Appointment;
//use Carbon\Carbon;

class OrderController extends BaseController
{
    public function CreateOrderForJoinToSchool(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'father_name' => 'required|string',
            'birthday' => 'required|date_format:Y-m-d',
            'gender' => 'required|in:0,1',
            'phone' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|email',
            'classification' => 'required|in:0,1',
            'class' => 'required|string',
            'year' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->responseError(['errors' => $validator->errors()]);
        }


            $new = new Order;

            $new->first_name = $request->first_name;
            $new->last_name = $request->last_name;
            $new->father_name = $request->father_name;
            $new->birthday = $request->birthday;
            $new->gender = $request->gender;
            $new->phone = $request->phone;
            $new->address = $request->address;
            $new->email = $request->email;
            $new->classification = $request->classification;
            $new->class = $request->class;
            $new->year = $request->year;
            $new->save();

            return $this->responseData("success",$new);
    }

    public function CreateOrderForCourse(Request $request, $course_id)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'father_name' => 'required|string',
            'mother_name'=>'required|string',
            'birthday' => 'required|date_format:Y-m-d',
            'gender' => 'required|in:0,1',
            'phone' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|email',
            'classification' => 'required|in:0,1',
            //'class' => 'required|string',
            //'year' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->responseError(['errors' => $validator->errors()]);
        }

            $new = new Order;

            $new->first_name = $request->first_name;
            $new->last_name = $request->last_name;
            $new->father_name = $request->father_name;
            $new->mother_name = $request->mother_name;
            $new->birthday = $request->birthday;
            $new->gender = $request->gender;
            $new->phone = $request->phone;
            $new->address = $request->address;
            $new->email = $request->email;
            $new->classification = $request->classification;
            //$new->class = $request->class;
            //$new->year = $request->year;
            $new->course_id = $course_id;
            $new->save();
            return $this->responseData("success",$new);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'father_name' => 'required|string',
            //'birthday' => 'required|date',
            'gender' => 'required|in:0,1',
            'phone' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|email',
            'classification' => 'required|in:0,1',
            //'class' => 'required|string',
            //'year' => 'required|integer',
                 ]);
                 $order = Order::find($id);
                     if(!$order)
        {
            return ['err' => 'not found'];
        }
      $order->update($request->all());
    //$data = $request->only(['first_name', 'last_name', 'father_name', 'gender', 'phone', 'address', 'email', 'classification']);
    //$order->update($data);
    $order->save();
        return $order;
    }
}
