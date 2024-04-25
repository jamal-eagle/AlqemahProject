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
    
    }


    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');

    }
}
