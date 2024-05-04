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
use App\Models\Homework;
use App\Models\Program_Student;
use App\Models\Note_Student;
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

    public function orderCourse($course_id)
    {
        $user = User::where('id',auth()->user()->id)->first();
        $new = new Order;

        $new->first_name = $user->first_name;
        $new->last_name = $user->last_name;
        $new->father_name = $user->father_name;
        //$new->mother_name = $user->mother_name;
        $new->birthday = $user->birthday;
        $new->gender = $user->gender;
        $new->phone = $user->phone;
        $new->address = $user->address;
        $new->email = $user->email;
        $new->classification = $user->classification;
        //$new->class = $request->class;
        //$new->year = $request->year;
        $new->course_id = $course_id;
        $new->save();
        return $this->responseData("success",$new);
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

    //عرض وظائف الطالب لمادة محددة
    public function homework_subject($subject_id)
    {
        $user= User::where('id',auth()->user()->id)->first();
        if (!$user) {
            return response()->json(['error' => 'user not found'], 404);
        }

        $homework = Homework::where('year',$user->year)->where('subject_id', $subject_id)->with('accessories')->get();

        return $homework;
    }

    // public function Read_File($accessori_id)
    // {
    //     $accessori = Accessories::where('id',$accessori_id)->get();
    //     return Response::download($filepath,$accessori->name.".".$file->extension);
    // }

    //عرض برنامج الدوام الخاص بالطالب
    public function programe()
    {
        $student = Student::where('user_id', auth()->user()->id)->first();

        $programe = Program_Student::where('section_id',$student->section_id)->with('image')->get();
        return $programe;
    }

    //عرض الملاحظات التي بحق الطالب
    public function display_note()
    {
        $student = Student::where('user_id', auth()->user()->id)->first();

        $note= Note_Student::where('student_id', $student->id)->with('user')->get();

        return $note;
    }

}
