<?php

namespace App\Http\Controllers\Api_teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Teacher;
use App\Models\Comment;
use App\Models\Student;

class PostController extends Controller
{
    //إنشاء مناقشة لشعبة محددة
    public function create_post(Request $request, $section_id)
    {
        $teacher = Teacher::where('user_id', auth()->user()->id)->first();
        $post = new Post;

        $post->quostion = $request->quostion;
        $post->year = Auth()->user()->year;
        $post->subject_id = $teacher->subject_id;
        $post->section_id = $section_id;
        $post->teacher_id = $teacher->id;

        $post->save();

        return $post;
    }

    //عرض مناقشاته
    public function display_post()
    {
        $teacher = Teacher::where('user_id', auth()->user()->id)->first();

        $post = Post::where('teacher_id', $teacher->id)->get();

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

     //تعديل تعليق
     public function editComment(Request $request, $comment_id)
     {
         $comment = Comment::find($comment_id);
         if (!$comment) {
             return ['err' => 'not found'];
         }

         elseif ($comment->student_id != null) {
             $student = Student::where('user_id', auth()->user()->id)->first();
             $comment2=Comment::where('student_id', $student->id)->first();
             
             $comment2->description = $request->description;
             $comment2->save();
             return $comment2;
         }

         elseif ($comment->teacher_id != null) {
             $teacher = Teacher::where('user_id', auth()->user()->id)->first();
             $comment2=Comment::where('teacher_id', $teacher->id)->first();
             
             $comment2->description = $request->description;
             $comment2->save();
             return $comment2;
             
         }

         return 'you can not edit this comment';
     }

             //حذف تعليق من قبل طالب أو أستاذ مع العلم يستطيع أي مستخدم حذف تعليق طالب عدا طالب آخر
             public function deleteComment ($comment_id)
             {
                 $student = Student::where('user_id', auth()->user()->id)->first();
                 $teacher =$teacher = Teacher::where('user_id', auth()->user()->id)->first();
                 $comment = Comment::where('id', $comment_id)->first();
     
                 if ($comment->student_id != NULL && auth()->user()->user_type == 'student') {
                     $comment = Comment::where('id', $comment_id)->where('student_id', $student->id)->delete();
                     return auth()->user();
                 }
     
                 elseif ($comment->teacher_id != NULL && auth()->user()->user_type == 'teacher') {
                     $comment_t = Comment::where('id', $comment_id)->where('teacher_id', $teacher->id)->delete();
                     return auth()->user();
                 }
     
                 elseif (auth()->user()->user_type != 'student')
                 {
                     $comment_t = Comment::where('id', $comment_id)->delete();
                     return auth()->user();
                 }
     
                 return 'you can not delete this comment';
             }
     
    
}

