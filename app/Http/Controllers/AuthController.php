<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function registerPost(Request $request)
    {
        $user = new User();

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->father_name = $request->father_name;
        $user->mother_name = $request->mother_name;
        $user->birthday = $request->birthday;
        $user->gender = $request->gender;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->year = $request->year;
        $user->image = $request->image;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->conf_password = Hash::make($request->conf_password);

        $user->save();

        return 'success';

    }

    public function login(Request $request)
    {
     $credetials = [
        'email' => $request->email,
        'password' => $request->password,
     ];

     if (Auth::attempt($credetials))
     {
        return 'success';
     }
     return 'error';
    }
}
