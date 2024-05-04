<?php

namespace App\Http\Controllers\Api_parentt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Parentt;

class ParenttController extends Controller
{
    //عرض جميع أبنائي المسجلين بالمعهد
    public function displayAllBaby()
    {
        $parent = Parentt::where('id', auth()->parent()->id)->first();
        //$student = Student::where('parentt_id', $parent->id)
        return $parent;
    }
}
