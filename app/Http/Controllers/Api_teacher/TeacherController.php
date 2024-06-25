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
use Illuminate\Support\Facades\Validator;
use App\Models\Image;
use App\Models\Image_Archive;
use App\Models\File_Archive;
use Illuminate\Http\UploadedFile;
use App\Models\Academy;

class TeacherController extends Controller
{
    //عرض برنامج الدوام الأستاذ
    public function programe()
{
    $teacher = Teacher::where('user_id', auth()->user()->id)->first();
    $programe = Program_Teachar::where('teacher_id',$teacher->id)->first();

    if ($programe) {
                    $img = Image::where('program_teacher_id', $programe->id)->latest()->first();
                    //return $img;
                        $imagePath = str_replace('\\', '/', public_path().'/upload/'.$img->path);
                        //return response()->file($imagePath);
                        if (file_exists($imagePath)) {
                            $img->image_url = asset('/upload/' . $img->path);
                            return response()->json([
                                // 'path' => $imagePath,
                                'image_info' => $img
                            ]);    
                        }
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'No images found'
            ]);
        }
}

    //إضافة ملاحظات لطالب معين
    public function add_note_about_student(Request $request ,$student_id)
    {
        $student = Student::find($student_id);
    if(!$student)
    {
        return response()->json(['the student not found']);
    }
    $validator = Validator::make($request->all(),[
        'text'=>'required|string',
        'type'=>'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $note = new Note_Student;

        $note->type = $request->type;
        $note->text = $request->text;
        $note->student_id = $student_id;
        $note->user_id = auth()->user()->id;

        $note->save();

        return $note;
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
    // $user= User::where('id',auth()->user()->id)->first();
    // if (!$user) {
    //     return response()->json(['error' => 'user not found'], 404);
    // }

    // $archive = Archive::where('year',$user->year)->where('subject_id', $subject_id)->with('image_Archives')->with('file_Archive')->get();

    // return $archive;
    $user= User::where('id',auth()->user()->id)->first();
    if (!$user) {
        return response()->json(['error' => 'user not found'], 404);
    }

    $archive = Archive::where('year',$user->year)->where('subject_id', $subject_id)->first();
    //صور السنة الحالية للمادة المحددة
    $image_select_year = Image_Archive::where('archive_id',$archive->id)->get();
    foreach ($image_select_year as $i) {
        $imagePath = str_replace('\\', '/', public_path().'/upload/'.$i->name);
                    //return response()->file($imagePath);
                    if (file_exists($imagePath)) {
                        $i->image_url = asset('/upload/' . $i->name);
                        $result[] = [
                            // 'path' => $imagePath,
                            'image_info' => $i
                        ];    
                    }
    }

    //صور السنة الحالية للمادة المحددة
    $file_select_year = File_Archive::where('archive_id',$archive->id)->get();
    foreach ($file_select_year as $f) {
        $filePath = str_replace('\\', '/', public_path().'/upload/'.$f->name);
                    //return response()->file($imagePath);
                    if (file_exists($imagePath)) {
                        $f->file_url = asset('/upload/' . $f->name);
                        $result[] = [
                            // 'path' => $filePath,
                            'file_info' => $f
                        ];    
                    }
    }
    //عم نشوف إذا في نتائج أو لاء
    if (!empty($result)) {
        // return response()->json([
        //     'status' => 'true',
        //     'images_files' => $result
        // ]);
        
        return $result;
    } else {
        return response()->json([
            'status' => 'false',
            'message' => 'No images found'
        ]);
    }

}

//حذف ملف أو صورة من ملفات السنة الحالية أو الأرشيفzahraa
public function delete_file_image($file_img_id, $imgFileName)
{
    $img = Image_Archive::find($file_img_id);
    $file = File_Archive::find($file_img_id);

    if ($img->name == $imgFileName) {
        // $img->delete();
        // return "delete image";

        // $image = Image::findOrFail($id);

    // حذفت الملف من المجلد يلي خزنتو في
    $imagePath = public_path().'/upload/'.$img->name;
    if(file_exists($imagePath)) {
        unlink($imagePath);
    }

    // حذفت الملف من الداتا عندي
    $img->delete();

    return response()->json([
        'status' => 'true',
        'message' => 'Image deleted successfully'
    ]);
    }
    elseif ($file->name == $imgFileName) {
        // $file->delete();
        // return "delete file";
        // حذفت الملف من المجلد يلي خزنتو في
    $filePath = public_path().'/upload/'.$file->name;
    if(file_exists($filePath)) {
        unlink($filePath);
    }

    // حذفت الملف من الداتا عندي
    $file->delete();

    return response()->json([
        'status' => 'true',
        'message' => 'File deleted successfully'
    ]);
    } 
        return "you do not have";

}

//رفع ملفات و صور للسنة الحاليةzahraa
public function upload_file_image(Request $request, $subject_id)
{
    $year_study = Academy::find(1);
    $archive = Archive::where('year',$year_study->year)->where('subject_id', $subject_id)->first();
    $validator = Validator::make($request->all(),[
        'name' => 'required|mimes:png,jpg,jpeg,gif,pdf,docx,txt'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'false',
            'message' => 'Please fix the errors',
            'errors' => $validator->errors()
        ]);
    }

    $img = $request->name;
    $ext = $img->getClientOriginalExtension();
    $imgFileName = time().'.'.$ext;
    $img->move(public_path().'/upload',$imgFileName);

    if ($ext=="png" || $ext=="jpg" || $ext=="jpeg" || $ext=="gif") {
        $image = new Image_Archive;
    $image->name = $imgFileName;
    $image->description = $request->description;
    $image->archive_id = $archive->id;
    $image->save();

    return response()->json([
        'status' => 'true',
        'message' => 'image upload success',
        'path' => asset('/upload/'.$imgFileName),
        'data' => $image
    ]);
    }
    
    elseif ($ext=="pdf" || $ext=="docx" || $ext=="txt") {
        $file = new File_Archive;
    $file->name = $imgFileName;
    $file->description = $request->description;
    $file->archive_id = $archive->id;
    $file->save();

    return response()->json([
        'status' => 'true',
        'message' => 'file upload success',
        'path' => asset('/upload/'.$imgFileName),
        'data' => $file
    ]);
    }

}

public function update_file_image(Request $request, $id)
{
    // التحقق من صحة البيانات المدخلة
    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|mimes:png,jpg,jpeg,gif,pdf,docx,txt',
        'description' => 'sometimes|string',
        'archive_id' => 'sometimes|integer'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'false',
            'message' => 'Please fix the errors',
            'errors' => $validator->errors()
        ]);
    }

    // البحث عن الملف أو الصورة في قاعدة البيانات
    $image = Image_Archive::find($id);
    $file = File_Archive::find($id);
    
    

    if (!$image && !$file) {
        return response()->json([
            'status' => 'false',
            'message' => 'File or image not found'
        ]);
    }

    // إذا تم تقديم ملف جديد
if ($request->hasFile('name')) {
    $img = $request->name;
    $ext = $img->getClientOriginalExtension();
    $imgFileName = time() . '.' . $ext;
    $img->move(public_path() . '/upload', $imgFileName);

    // تحديث السجل بناءً على النوع (صورة أو ملف)
    if ($image) {
        $extension = $image->getClientOriginalExtension();
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    if ($request->has('description')) {
            $image->description = $request->description;
        }
        if ($request->has('archive_id')) {
            $image->archive_id = $request->archive_id;
        }
        
        $image->save();

        return response()->json([
            'status' => 'true',
            'message' => 'Image updated successfully',
            'data' => $image
        ]);
    }
} elseif ($file) {
    $extension = $file->getClientOriginalExtension();
    if (in_array($extension, ['pdf', 'doc', 'docx', 'txt'])) {
        if ($request->has('description')) {
            $file->description = $request->description;
        }
        if ($request->has('archive_id')) {
            $file->archive_id = $request->archive_id;
        }
        $file->save();
        
        return response()->json([
            'status' => 'true',
            'message' => 'File updated successfully',
            'data' => $file
        ]);
    }
}
}
//     if ($image) {
//         // تحديث السجل في جدول الصور
//         $image->name = $imgFileName;
//         $image->save();
//     } elseif ($file) {
//         // تحديث السجل في جدول الملفات
//         $file->name = $imgFileName;
//         $file->save();
//     }
// }

// // تحديث السجل بناءً على النوع (صورة أو ملف)
// if ($image) {
//     $ext_img = pathinfo($image->name, PATHINFO_EXTENSION);
//     if (in_array($ext_img, ['png', 'jpg', 'jpeg', 'gif'])) {
//         $ext_img = pathinfo($image->name, PATHINFO_EXTENSION);
//         if (($ext_img=="png" || $ext_img=="jpg" || $ext_img=="jpeg" || $ext_img=="gif") || ($ext=="png" || $ext=="jpg" || $ext=="jpeg" || $ext=="gif")) {
//             if ($request->has('description')) {
//                 $image->description = $request->description;
//             }
//             if ($request->has('archive_id')) {
//                 $image->archive_id = $request->archive_id;
//             }
            
//             $image->save();
    
//             return response()->json([
//                 'status' => 'true',
//                 'message' => 'Image updated successfully',
//                 'data' => $image
//             ]);
//         }
//     }
// } elseif ($file) {
//     $ext_file = pathinfo($file->name, PATHINFO_EXTENSION);
//     if (in_array($ext_file, ['pdf', 'docx', 'txt'])) {
//         $ext_file = pathinfo($file->name, PATHINFO_EXTENSION);
//             if (($ext_file=="pdf" || $ext_file=="docx" || $ext_file=="txt") || ($ext=="pdf" || $ext=="docx" || $ext=="txt")) {
//                 if ($request->has('description')) {
//                     $file->description = $request->description;
//                 }
//                 if ($request->has('archive_id')) {
//                     $file->archive_id = $request->archive_id;
//                 }
//                 $file->save();
        
//                 return response()->json([
//                     'status' => 'true',
//                     'message' => 'File updated successfully',
//                     'data' => $file
//                 ]);
//             }
//     }
// }


    // // إذا تم تقديم ملف جديد
    // if ($request->hasFile('name')) {
    //     $img = $request->name;
    //     $ext = $img->getClientOriginalExtension();
    //     $imgFileName = time() . '.' . $ext;
    //     $img->move(public_path() . '/upload', $imgFileName);

    //     if ($image) {
    //         $image->name = $imgFileName;
    //     } elseif ($file) {
    //         $file->name = $imgFileName;
    //     }
    // }

    // // تحديث السجل بناءً على النوع (صورة أو ملف)
    // if ($image) {
    //     $ext_img = pathinfo($image->name, PATHINFO_EXTENSION);
    //     if (($ext_img=="png" || $ext_img=="jpg" || $ext_img=="jpeg" || $ext_img=="gif") || ($ext=="png" || $ext=="jpg" || $ext=="jpeg" || $ext=="gif")) {
    //         if ($request->has('description')) {
    //             $image->description = $request->description;
    //         }
    //         if ($request->has('archive_id')) {
    //             $image->archive_id = $request->archive_id;
    //         }
            
    //         $image->save();
    
    //         return response()->json([
    //             'status' => 'true',
    //             'message' => 'Image updated successfully',
    //             'data' => $image
    //         ]);
    //     }
    //     elseif ($file) {
    //         $ext_file = pathinfo($file->name, PATHINFO_EXTENSION);
    //         if (($ext_file=="pdf" || $ext_file=="docx" || $ext_file=="txt") || ($ext=="pdf" || $ext=="docx" || $ext=="txt")) {
    //             if ($request->has('description')) {
    //                 $file->description = $request->description;
    //             }
    //             if ($request->has('archive_id')) {
    //                 $file->archive_id = $request->archive_id;
    //             }
    //             $file->save();
        
    //             return response()->json([
    //                 'status' => 'true',
    //                 'message' => 'File updated successfully',
    //                 'data' => $file
    //             ]);
            // }
            
            
        // } 
    // } 
}

// public function update_file_image(Request $request, $id)
// {
//     // التحقق من صحة البيانات المدخلة
//     $validator = Validator::make($request->all(),[
//         'description' => 'sometimes|string',
//         'archive_id' => 'sometimes|integer'
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'status' => 'false',
//             'message' => 'Please fix the errors',
//             'errors' => $validator->errors()
//         ]);
//     }

//     // البحث عن الملف أو الصورة في قاعدة البيانات
//     $image = Image_Archive::find($id);
//     $file = File_Archive::find($id);
//     pathinfo($image->name, PATHINFO_EXTENSION);

//     if (!$image && !$file) {
//         return response()->json([
//             'status' => 'false',
//             'message' => 'File or image not found'
//         ]);
//     }

//     // تحديث السجل بناءً على النوع (صورة أو ملف)
//     if ($image) {
//         if ($request->has('description')) {
//             $image->description = $request->description;
//         }
//         if ($request->has('archive_id')) {
//             $image->archive_id = $request->archive_id;
//         }
//         $image->save();

//         return response()->json([
//             'status' => 'true',
//             'message' => 'Image updated successfully',
//             'data' => $image
//         ]);
//     } elseif ($file) {
//         if ($request->has('description')) {
//             $file->description = $request->description;
//         }
//         if ($request->has('archive_id')) {
//             $file->archive_id = $request->archive_id;
//         }
//         $file->save();

//         return response()->json([
//             'status' => 'true',
//             'message' => 'File updated successfully',
//             'data' => $file
//         ]);
//     }
// }




    //عرض السنوات التي تحتوي ملفات للأرشيف حسب المادة
    public function display_year_archive($subject_id)
    {
        $archive = Archive::where('subject_id',$subject_id)->get();
        return $archive;
    }

    //عرض ملفات و صور مادة محددة حسب سنة محددة
    public function file_image_subject_year($subject_id,$year)
    {
        $archive = Archive::where('subject_id',$subject_id)->where('year', $year)->first();
        //صور السنة الحالية للمادة المحددة
        $image_select_year = Image_Archive::where('archive_id',$archive->id)->get();
        foreach ($image_select_year as $i) {
            $imagePath = str_replace('\\', '/', public_path().'/upload/'.$i->name);
                        //return response()->file($imagePath);
                        if (file_exists($imagePath)) {
                            $i->image_url = asset('/upload/' . $i->name);
                            $result[] = [
                                // 'path' => $imagePath,
                                'image_info' => $i
                            ];    
                        }
        }

        //صور السنة الحالية للمادة المحددة
        $file_select_year = File_Archive::where('archive_id',$archive->id)->get();
        foreach ($file_select_year as $f) {
            $filePath = str_replace('\\', '/', public_path().'/upload/'.$f->name);
                        //return response()->file($imagePath);
                        if (file_exists($filePath)) {
                            $f->image_url = asset('/upload/' . $f->name);
                            $result[] = [
                                // 'path' => $filePath,
                                'file_info' => $f
                            ];    
                        }
        }
        //عم نشوف إذا في نتائج أو لاء
        if (!empty($result)) {
            return response()->json([
                'status' => 'true',
                'files' => $result
            ]);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'No images found'
            ]);
        }
    }

//رفع ملف أو صورة من ملفات الأرشيف
public function upload_file_image_archive(Request $request, $archive_id)
{
    // public function upload_file_image(Request $request, $subject_id)
    $validator = Validator::make($request->all(),[
        'name' => 'required|mimes:png,jpg,jpeg,gif,pdf,docx,txt'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'false',
            'message' => 'Please fix the errors',
            'errors' => $validator->errors()
        ]);
    }

    $img = $request->name;
    $ext = $img->getClientOriginalExtension();
    $imgFileName = time().'.'.$ext;
    $img->move(public_path().'/upload',$imgFileName);

    if ($ext=="png" || $ext=="jpg" || $ext=="jpeg" || $ext=="gif") {
        $image = new Image_Archive;
    $image->name = $imgFileName;
    $image->description = $request->description;
    $image->archive_id = $archive_id;
    $image->save();

    return response()->json([
        'status' => 'true',
        'message' => 'image upload success',
        'path' => asset('/upload/'.$imgFileName),
        'data' => $image
    ]);
    }
    
    elseif ($ext=="pdf" || $ext=="docx" || $ext=="txt") {
        $file = new File_Archive;
    $file->name = $imgFileName;
    $file->description = $request->description;
    $file->archive_id = $archive_id;
    $file->save();

    return response()->json([
        'status' => 'true',
        'message' => 'file upload success',
        'path' => asset('/upload/'.$imgFileName),
        'data' => $file
    ]);
    
}
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
    // $student = Student::with('user')->find($student_id);
    //     if(!$student)
    //     {
    //         return response()->json('the student not found ');
    //     }

    //     return response()->json(['student' => $student, 'message' => 'Success']);
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


//عرض جميع الطلاب الذي يدرسهم حسب الترتيب الأبجدي
public function display_all_students_I_teach()
{
    $teacher = Teacher::where('user_id', auth()->user()->id)->first();
    $sections = Teacher_section::where('teacher_id', $teacher->id)->get();
    $students = collect();

    foreach ($sections as $section) {
        $students = $students->merge(Student::where('section_id', $section->id)->with('user')->get());
    }

    $sortedStudents = $students->sortBy(function($student) {
        return $student->user->first_name;
    });

    return $sortedStudents->values()->all();

//     عرض جميع الطلاب الذين يدرسهم ولكن دون ترتيب أبجدي
//     $teacher = Teacher::where('user_id', auth()->user()->id)->first();
//     $sections = Teacher_section::where('teacher_id', $teacher->id)->get();
//     $students = [];

//     foreach ($sections as $section) {
//         $students[] = Student::where('section_id', $section->id)->with('user')->get();
//     }

//     return $students;
 }
























public function upload(Request $request)
{
    //Route::post('/upload',[TeacherController::class,'upload']);
    $validator = Validator::make($request->all(),[
        'path' => 'required|mimes:png,jpg,jpeg,gif,pdf,docx,txt'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'false',
            'message' => 'Please fix the errors',
            'errors' => $validator->errors()
        ]);
    }

    $img = $request->path;
    $ext = $img->getClientOriginalExtension();
    $imageName = time().'.'.$ext;
    $img->move(public_path().'/upload',$imageName);

    $image = new Image;
    $image->path = $imageName;
    $image->save();

    return response()->json([
        'status' => 'true',
        'message' => 'image upload success',
        'path' => asset('/upload/'.$imageName),
        'data' => $image
    ]);
}


public function showImage($path)
{
    $image = public_path().'/upload/'.$path;

    if (file_exists($image)) {
        return response()->file($image);
    } else {
        return response()->json([
            'status' => 'false',
            'message' => 'Image not found'
        ]);
    }
}


public function delete($id)
{
    $image = Image::findOrFail($id);

    // حذفت الملف من المجلد يلي خزنتو في
    $imagePath = public_path().'/upload/'.$image->path;
    if(file_exists($imagePath)) {
        unlink($imagePath);
    }

    // حذفت الملف من الداتا عندي
    $image->delete();

    return response()->json([
        'status' => 'true',
        'message' => 'Image deleted successfully'
    ]);
}

//تابع تعديل
public function update_image(Request $request, $id)
{
    // التحقق من المدخلات
    $validator = Validator::make($request->all(),[
        'path' => 'required|mimes:png,jpg,jpeg,gif,pdf,docx,txt'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'false',
            'message' => 'Please fix the errors',
            'errors' => $validator->errors()
        ]);
    }

    // استرجاع الصورة القديمة بناءً على الـ ID
    $image = Image::find($id);

    if (!$image) {
        return response()->json([
            'status' => 'false',
            'message' => 'Image not found'
        ]);
    }

    // حذف الصورة القديمة من المجلد إذا كانت موجودة
    $oldImagePath = public_path().'/upload/'.$image->path;
    if (file_exists($oldImagePath)) {
        unlink($oldImagePath);
    }

    // رفع الصورة الجديدة
    $img = $request->path;
    $ext = $img->getClientOriginalExtension();
    $imageName = time().'.'.$ext;
    $img->move(public_path().'/upload', $imageName);

    // تحديث مسار الصورة في قاعدة البيانات
    $image->path = $imageName;
    $image->save();

    return response()->json([
        'status' => 'true',
        'message' => 'Image updated successfully',
        'path' => asset('/upload/'.$imageName),
        'data' => $image
    ]);
}


public function add_course()
{

}


}
