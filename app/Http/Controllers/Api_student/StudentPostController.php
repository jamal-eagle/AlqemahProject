<?php

namespace App\Http\Controllers\Api_student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;

class StudentPostController extends Controller
{
        //عرض جميع المناقشات الخاصة بشعبة الطالب فقط عنوان و اسم المدرس
        public function displayAllPost()
        {
            $student = Student::where('user_id', auth()->user()->id)->first();
            $post = Post::where('section_id',$student->section_id)->with('subject')->with('teacher.user')->get();
            return $post;
        }
    
        //عرض مناقشة محددة التعليقات و السؤال
        public function displayPost($post_id)
        {
            $post = Post::where('id',$post_id)->with('comments.student.user')->with('comments.teacher.user')->first();
            return $post;
        }
    
        //إضافة تعليق لمناقشة محددة من قبل طالب أو أستاذ
        public function addComment(Request $request, $post_id)
        {
            $comment = new Comment;
    
            $comment->description = $request->description;
            $comment->post_id = $post_id;
    
            if(auth()->user()->user_type == 'student')
            {
                $student = Student::where('user_id', auth()->user()->id)->first();
                $comment->student_id = $student->id;
            }
            elseif(auth()->user()->user_type == 'teacher')
            {
                $teacher = Teacher::where('user_id', auth()->user()->id)->first();
                $comment->teacher_id = $teacher->id;
            }
            $comment->save();
        }
}
