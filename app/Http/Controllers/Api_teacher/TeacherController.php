<?php

namespace App\Http\Controllers\Api_teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Program_Teachar;
use App\Models\Student;
use App\Models\Note_Student;

class TeacherController extends Controller
{
    public function programe()
    {
        $teacher = Teacher::where('user_id', auth()->user()->id)->first();

        $programe = Program_Teachar::where('teacher_id',$teacher->id)->with('image')->get();
        return $programe;
    }

    public function add_note_about_student(Request $request ,$student_id)
    {
        $student = Student::where('id', $student_id)->first();

        $note = new Note_Student;

        $note->text = $request->text;
        $note->student_id = $student_id;
        $note->user_id = auth()->user()->id;
    }
}
