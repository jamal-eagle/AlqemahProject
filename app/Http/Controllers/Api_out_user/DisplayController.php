<?php

namespace App\Http\Controllers\Api_out_user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Teacher;
use App\Models\Course;

class DisplayController extends Controller
{
    /*------------------------teacher------------------------*/
    public function all_teatcher()
    {
        $teatcher = Teacher::with('user')->get();
        return $teatcher;
    }

    public function info_teatcher($teatcher_id)
    {
        $teatcher = Teacher::where('id',$teatcher_id)->with('user')->with('subject')->get();
        return $teatcher;
    }

    /*------------------------course------------------------*/
    public function all_course()
    {
        $course = Course::with('subject')->with('classs')->with('teacher.user')->get();
        return $course;
    }

    public function info_course($id_course)
    {
        $course = Course::where('id', $id_course)->with('subject')->with('classs')->with('teacher.user')->get();
        return $course;
    }
}
