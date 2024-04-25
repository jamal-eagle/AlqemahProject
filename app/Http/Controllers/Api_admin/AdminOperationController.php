<?php

namespace App\Http\Controllers\Api_admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
                return $this->responseData($token,'User login successfully');
            }
        }else{
            return $this->responseError(['please  check your Auth','auth error']);
        }

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->responseError(['the user logged out']);
    }




}
