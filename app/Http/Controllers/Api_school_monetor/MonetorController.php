<?php

namespace App\Http\Controllers\Api_school_monetor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Publish;
use App\Models\Mark;
use App\Models\Classs;
use App\Models\Note_Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Teacher_subject;
use App\Models\Subject;

class MonetorController extends Controller
{
    public function student_classification($classifiaction)
    {
        if($classifiaction = 1){
        $stud =Student::where('classifiaction' ,'=', 1)->get();
        $student = User::where($stud->user_id, 'id')->get()->all();
            return response()->json([$stud,$student]);
        }
        else {
            $stud =Student::where('classifiaction' ,'=', 0)->get();
            $student = User::where($stud->user_id, 'id')->get()->all();
                return response()->json([$stud,$student]);
            }
    }

    public function desplay_all_student_regester($year)
    {
        $student = User::where('year',$year)->with('student')->get()->all();
        return response()->json([$student,'all student regester here']);
    }

    public function desplay_classs_and_section()
    {
        $classs = Classs::get()->all();
        if(!$classs)
        {
            return response()->json(['you havenot any class']);
        }
        $classs1 =  $classs->section;
        return response()->json([$classs,$classs1,'successsssssss']);
    }

    public function show_profile_student($student_id)
    {
        $student = Student::find($student_id);
        if(!$student)
        {
            return response()->json('the student not found ');
        }
        $student->user;
        return response()->json([$student,'sucsseesss ']);

    }
    public function update_profile_student(Request $request,$student_id)
    {
        $student = Student::find($student_id);
        if(!$student)
        {
            return response()->json('the student not found ');
        }
        $validator = Validator::make($request->all(),[
            'calssification' => 'required',
            'school_tuition'=>'required',
            'class_id'=>'required',
            'section_id'=>'required',
            'parentt_id'=>'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $user = $student->user;
        $user_id = $user->id;
        $student->calssification = $request->calssification;
        $student->school_tuition = $request->school_tuition;
        $student->user_id = $user_id;
        $student->class_id =$request->class_id;
        $student->section_id= $request->section_id;
        $student->parentt_id = $request->parentt_id;

        $student->update();
        return response()->json(['sucussssss']);

    }

    public function desplay_student_marks($student_id)
    {
        $student = Student::find($student_id);
        if(!$student)
        {
            return response()->json(['student not found ']);
        }
        $student->mark;
        return response()->json([$student,'sucssssss']);

    }

    public function desplay_student_note($student_id)
    {
        $student = Student::find($student_id);
        if(!$student)
        {
            return response()->json(['student not found ']);
        }
        $student->note_students;
        return response()->json([$student,'sucssssss']);

    }

    public function create_note_student(Request $request , $student_id)
    {
        $student = Student::find($student_id);
        if(!$student)
        {
            return response()->json(['the student not found']);
        }
        $validator = Validator::make($request->all(),[
            'text'=>'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }

            $note_student = new Note_Student();
            $note_student->text = $request->text;
            $note_student->student_id = $student_id;
            $note_student->user_id = auth()->user()->id;

            $note_student->save();

            return response()->json(['successssss']);

    }

    public function all_teatcher()
    {
        $teatcher = Teacher::with('user')->get();
        return $teatcher;
    }
    public function info_teatcher($teatcher_id)
    {
        $teacher = Teacher::where('id', $teatcher_id)->first();
        $teacher_subject = Teacher_subject::where('teacher_id', $teacher->id)->get();

        foreach ($teacher_subject as $subject) {
            $info_subject = Subject::where('id', $subject->subject_id)->get();
            $result[] = $info_subject;
        }
        return [$teacher, $result];
    }

    public function desplay_teacher_course($teacher_id)
    {
        $teacher = Teacher::find($teacher_id);
        if(!$teacher)
        {
            return response()->json(['teacher not found ']);
        }

        return  $teacher->course;

    }
    public function info_course($id_course)
    {
        $course = Course::where('id', $id_course)->with('subject')->with('classs')->with('teacher.user')->get();
        return $course;
    }

    public function desplay_publish()
    {
        $publish = Publish::get()->all();
        return response()->json([$publish,'this is all publish']);
    }
    public function DisplayOrderNewStudent()
    {
        $order = DB::table('orders')->where('student_id','=',null)->where('course_id','=',null)->get();

        return $order;
    }

    public function add_publish(Request $request)
    {
    $validator = Validator::make($request->all(),[
        'description'=>'required|string',
        'course_id'=>'required',
        ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()]);
    }

    $publish = new Publish();
    $publish->description = $request->description;
    $publish->course_id = $request->course_id;
    $publish->save();
    return response()->json(['sucssscceccs']);
    }

    public function delete_publish($publish_id)
    {
        $publish = Publish::find($publish_id);
        if(!$publish)
        {
            return response()->json(['the publish not found or was deleted  ']);
        }
        $publish->delete();
        return response()->json(['the publish  deleted  ']);

    }
        public function update_publish(Request $request,$publish_id)
    {
        $publish = Publish::find($publish_id);
        if(!$publish)
        {
            return response()->json(['the publish not found']);
        }
        $validator = Validator::make($request->all(),[
            'description'=>'required|string',
            'course_id'=>'required',
            ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $publish->description = $request->description;
        $publish->course_id = $request->course_id;
        $publish->update();
        return response()->json(['sucssscceccs']);
    }

    public function add_mark_to_student(request $request,$student_id)
    {
        $student = Student::find($student_id);
        if(!$student)
        {
            return response()->json(['the student not found']);
        }

        $mark = new Mark;
        $mark->ponus = $request->ponus  ;
        $mark->homework = $request->homework || null;
        $mark->oral = $request->oral || null;
        $mark->test1 = $request->test1 || null;
        $mark->test2 = $request->test2 || null;
        $mark->exam_med = $request->exam_med || null;
        $mark->exam_final = $request->exam_final || null;
        $aggregrate = ($request->ponus + $request->homework
        + $request->oral + $request->test1
        +$request->test2 + $request->exam_med
        +$request->exam_final);
        if ($aggregrate > 50){
        $mark->state = 1;
        }
        else {
            $mark->state = 0;
        }
        $mark->student_id = $student_id;
        $mark->subject_id = $request->subject_id;
        $mark->save();

        return response()->json(['succusssss']);

    }

}
