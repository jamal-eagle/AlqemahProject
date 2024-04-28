<?php

namespace App\Http\Controllers\Api_student;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use App\Models\Classs;
use App\Models\Subject;
use App\Models\Archive;
use Illuminate\Support\Facades\Auth;

class Student_operationController extends BaseController
{
    //عرض مواد الطالب
    public function display_subject(Request $request)
    {

        $student = Student::where('user_id',$request->user()->id)->first();
        //$student = Student::where('user_id', $user_id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $subject = Subject::where('class_id', $student->class_id)->get();

        return $subject;

    }

    //عرض صور و ملفات مواد الطالب
    public function display_file_subject($subject_id)
    {
        $user= User::where('id',auth()->user()->id)->first();
        if (!$user) {
            return response()->json(['error' => 'user not found'], 404);
        }

        $archive = Archive::where('year',$user->year)->where('subject_id', $subject_id)->with('image_Archives')->with('file_Archive')->get();

        return $archive;
    }

    //عرض الدورات التي سجل فيها الطالب
    public function my_course()
    {
        $student = Student::where('user_id', auth()->user()->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }
        $order = Order::where('student_id', $student->id)->with('course')->get();

        return $order;
    }




}
