<?php

namespace App\Http\Controllers\Api_teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Program_Teachar;
use App\Models\Student;
use App\Models\Note_Student;
use App\Models\Out_Of_Work_Employee;
use App\Models\User;
use App\Models\Archive;
use App\Models\Section;
use App\Models\Teacher_section;
use App\Models\Mark;
use App\Models\Teacher_subject;
use App\Models\Subject;

class TeacherController extends Controller
{
    //عرض برنامج الدوام الأستاذ
    public function programe()
    {
        $teacher = Teacher::where('user_id', auth()->user()->id)->first();

        $programe = Program_Teachar::where('teacher_id',$teacher->id)->with('image')->get();
        return $programe;
    }

    //إضافة ملاحظات لطالب معين
    public function add_note_about_student(Request $request ,$student_id)
    {
        $student = Student::where('id', $student_id)->first();

        $note = new Note_Student;

        $note->text = $request->text;
        $note->student_id = $student_id;
        $note->user_id = auth()->user()->id;
    }

    //غيابات المدرس
    public function out_work_teacher()
    {
        $teacher = Teacher::where('user_id', auth()->user()->id)->first();

        $out_work = Out_Of_Work_Employee::where('teacher_id', $teacher->id)->get();
        return $out_work;
    }

    //عرض المواد مع الصف التي أدرسها
    public function display_supject_with_class()
    {
        $teacher = Teacher::where('user_id', auth()->user()->id)->first();
        $teacher_subject = Teacher_subject::where('teacher_id', $teacher->id)->get();

        foreach ($teacher_subject as $subject) {
            $info_subject = Subject::where('id', $subject->subject_id)->with('classs')->get();
            $result[] = $info_subject;
        }
        return $result;

    }

    //عرض المواد التي أدرسها
    public function display_supject()
    {
        $teacher = Teacher::where('user_id', auth()->user()->id)->first();
        $teacher_subject = Teacher_subject::where('teacher_id', $teacher->id)->get();

        foreach ($teacher_subject as $subject) {
            $info_subject = Subject::where('id', $subject->subject_id)->get();
            $result[] = $info_subject;
        }
        return $result;

    }

    //عرض صور و ملفات المادة التي يعطيها للسنة الحالية
public function display_file_subject($subject_id)
{
    $user= User::where('id',auth()->user()->id)->first();
    if (!$user) {
        return response()->json(['error' => 'user not found'], 404);
    }

    $archive = Archive::where('year',$user->year)->where('subject_id', $subject_id)->with('image_Archives')->with('file_Archive')->get();

    return $archive;
}

//حذف ملف أو صورة من ملفات السنة الحالية
public function delete_file_image()
{

}

public function upload_file_image()
{

}


//عرض ملفات الأرشيف لسنة محددة
public function display_file_image_archive($subject_id,$year)
{  
    $archive = Archive::where('year',$year)->where('subject_id', $subject_id)->with('image_Archives')->with('file_Archive')->get();
    return $archive;
}

//حذف ملف أو صورة من ملفات الأرشيف
public function delete_file_image_archive()
{

}

//رفع ملف أو صورة من ملفات الأرشيف
public function upload_file_image_archive()
{
    
}

//الصفوف الذي يعطيه المدرس
public function classs()
{
        $teacher = Teacher::where('user_id', auth()->user()->id)->first();
        $teacher_subject = Teacher_subject::where('teacher_id', $teacher->id)->get();
        foreach ($teacher_subject as $subject) {
           $info_subject = Subject::find($subject->subject_id);
            $result[] = $info_subject->classs;
        }
        return $result;
 
}

public function suction($class_id)
{
     $teacher = Teacher::where('user_id', auth()->user()->id)->first();
    $all_section_class = Section::where('class_id', $class_id)->get();
    $class = Teacher_section::where('teacher_id',$teacher->id)->get();
    
    foreach ($all_section_class as $section) {
        $section_id = $section->id;
        foreach ($class as $sectionclass)
        {
            $sectionclass_id = $sectionclass->section_id;
            if ($section_id == $sectionclass_id) {
                //echo $section;
                $result[] = $section;
            }
        }
        
    }

    return $result;
}



// public function classs()
// {
//     $teacher = Teacher::where('user_id', auth()->user()->id)->with('subject.classs.section')->get();
//     return $teacher;
 
// }
// public function classs()
// {
//     $teacher = Teacher::where('user_id', auth()->user()->id)
//         ->with('subject.classs:id,name,section')
//         ->get();

//     return $teacher;
// }



//الشعب و الصف الذي يعطيها المدرس
// public function classs()
// {
//     $teacher = Teacher::where('user_id', auth()->user()->id)->with('teacher_section.section.classs')->get();
//     return $teacher;
// }

//عرض طلاب شعبة محددة
public function display_student_section($section_id)
{
    $student = Student::where('section_id', $section_id)->with('user')->get();

    return $student;
}

//عرض معلومات طالب
public function display_info_student($student_id)
{
    $student = Student::where('id', $student_id)->with('user')->first();
    return $student;
}

//عرض علامات طالب لمادة محددة حسب المادة التي يعطيها المدرس
public function display_mark($student_id)
{
    $teacher = Teacher::where('user_id', auth()->user()->id)->with('subject')->first();

    if ($teacher && $teacher->subject) {
        $mark = Mark::where('student_id', $student_id)->where('subject_id', $teacher->subject->id)->first();
        return $mark;
    }

    return null;
}

//تعديل علامة طالب
public function edit_mark(Request $request,$mark_id)
{

    $mark = Mark::where('id', $mark_id)->first();
    if ($request->has('ponus')) {
        $mark->ponus = $request->ponus;
    }

    if ($request->has('homework')) {
        $mark->homework = $request->homework;
    }

    if ($request->has('oral')) {
        $mark->oral = $request->oral;
    }

    if ($request->has('test1')) {
        $mark->test1 = $request->test1;
    }

    if ($request->has('test2')) {
        $mark->test2 = $request->test2;
    }

    if ($request->has('exam_med')) {
        $mark->exam_med = $request->exam_med;
    }

    if ($request->has('exam_final')) {
        $mark->exam_final = $request->exam_final;
    }

    if ($request->has('state')) {
        $mark->state = $request->state;
    }
    $mark->student_id = $mark->student_id;
    $mark->subject_id = $mark->subject_id; 
    
    $mark->save();
    return $mark;

}




}
