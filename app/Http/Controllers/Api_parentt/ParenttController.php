<?php

namespace App\Http\Controllers\Api_parentt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Parentt;
use App\Models\Program_Student;
use App\Models\Subject;
use App\Models\Homework;
use App\Models\Note_Student;
use App\Models\Mark;

class ParenttController extends Controller
{
    //عرض جميع أبنائي المسجلين بالمعهد
    public function displayAllBaby(Request $request)
    {
    $parent = Parentt::where('id', auth()->user()->id)->with('student.user')->get();
    return $parent;
    }

    //برنامج الدوام الخاص بالابن المحدد
    public function displayPrograme($student_id)
    {
        $student = Student::where('id', $student_id)->first();
        $programe = Program_Student::where('section_id',$student->section_id)->with('image')->get();
        return $programe;
    }

    //عرض مواد ابني
    public function displaySubjectSun($student_id)
    {
        $student = Student::where('id', $student_id)->first();
        $subject = Subject::where('class_id', $student->class_id)->get();
        return $subject;
    }

    //عرض وظائف ابني لمادة محددة
    public function homework_subject_my_sun($student_id, $subject_id)
    {
        $student = Student::where('id', $student_id)->with('user')->first();
        $year = $student->user->year;
        $homework = Homework::where('year',$year)->where('subject_id', $subject_id)->with('accessories')->get();        
        return $homework;
    }

    //عرض الملاحظات التي بحق الابن
    public function display_note($student_id)
    {
         $note= Note_Student::where('student_id', $student_id)->with('user')->get();
         return $note;
    }

    //عرض علامات الابن
    public function displayMark($student_id)
    {
        $mark = Mark::where('student_id', $student_id)->with('subject')->get();
        return $mark;
    }
}
