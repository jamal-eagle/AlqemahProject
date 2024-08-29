<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Actions_log; // استدعاء موديل ActionLog لتسجيل الأنشطة
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (Auth::check()) {
        //     $user = Auth::user();

        //     // تسجيل النشاط لكل المستخدمين
        //     Actions_log::create([
        //         'user_id' => $user->id,  // ID الخاص بالمستخدم
        //         'action' => $request->method() . ' ' . $request->path(), // نوع العملية والمسار
        //         'description' => 'قام المستخدم بطلب ' . $request->path(), // وصف للعملية
        //     ]);
        // }

        // return $next($request);

        if (Auth::check()) {
            $user = Auth::user();

            // إنشاء وصف مخصص بناءً على الـ path
            $description = $this->getActionDescription($request->path());

            // تسجيل النشاط
            Actions_log::create([
                'user_id' => $user->id,
                'action' => $request->method() . ' ' . $request->path(),
                'description' => $description,
            ]);
        }

        return $next($request);
    }

    /**
     * الحصول على الوصف بناءً على الـ path المطلوب
     */
    // private function getActionDescription($path)
    // {

    //     if (preg_match('/^api\/monetor\/update_profile_student\/\d+$/', $path)) {
    //         return 'تم تحديث معلومات طالب';
    //     }

    //     // أضف منطقًا مخصصًا لكل API هنا
    //     switch ($path) {
    //         // case 'api/monetor/desplay_publish':
    //         //     return 'عرض المنشورات من قبل المستخدم';
    //         // case 'api/monetor/create_publish':
    //         //     return 'إنشاء منشور جديد';
    //         // case 'api/monetor/update_publish':
    //         //     return 'تحديث منشور قائم';
    //         // // أضف المزيد من الحالات حسب الحاجة...
    //         // default:
    //         //     return 'قام المستخدم بطلب ' . $path;

    //         case 'api/monetor/update_profile_student':
    //             return 'تم تحديث معلومات طالب';
    //         default:
    //             return 'قام المستخدم بطلب ' . $path;
    //     }
    // }

//     private function getActionDescription($path)
// {
//     // تحديث معلومات طالب
//     if (preg_match('/^api\/monetor\/update_profile_student\/(\d+)$/', $path, $matches)) {
//         // استخراج معرف الطالب من الـ path
//         $studentId = $matches[1];

//         // جلب بيانات الطالب عبر العلاقة بين User و Student
//         $student = \App\Models\Student::find($studentId);

//         // إذا كان الطالب موجودًا، جلب المستخدم المرتبط بالطالب وعرض اسمه
//         if ($student && $student->user) {
//             $user = $student->user;
//             return 'تم تحديث معلومات الطالب: ' . $user->first_name . ' ' . $user->last_name;
//         } else {
//             return 'تم تحديث معلومات طالب غير معروف (ID: ' . $studentId . ')';
//         }
//     }



//     // التعامل مع مسارات أخرى
//     switch ($path) {
//         case 'api/monetor/desplay_publish':
//             return 'عرض المنشورات من قبل المستخدم';
//         // أضف المزيد من الحالات حسب الحاجة...
//         default:
//             return 'قام المستخدم بطلب ' . $path;
//     }
// }


private function getActionDescription($path)
{
    // تحديث معلومات طالب
    if (preg_match('/^api\/monetor\/update_profile_student\/(\d+)$/', $path, $matches)) {
        // استخراج معرف الطالب من الـ path
        $studentId = $matches[1];

        // جلب بيانات الطالب عبر العلاقة بين User و Student
        $student = \App\Models\Student::find($studentId);

        // إذا كان الطالب موجودًا، جلب المستخدم المرتبط بالطالب وعرض اسمه
        if ($student && $student->user) {
            $user = $student->user;
            return 'تم تحديث معلومات الطالب: ' . $user->first_name . ' ' . $user->last_name;
        } else {
            return 'تم تحديث معلومات طالب غير معروف (ID: ' . $studentId . ')';
        }
    }

    // تحقق من المسارات التي تحتوي على معرفات ديناميكية باستخدام التعبيرات النمطية
    if (preg_match('/^api\/monetor\/create_note\/(\d+)$/', $path, $matches)) {
        $studentId = $matches[1];
        $student = \App\Models\Student::find($studentId);
        return $student ? 'تم إرسال ملاحظة /إنذار للطالب: ' . $student->user->first_name . ' ' . $student->user->last_name : 'تم إرسال ملاحظة لطالب غير معروف';
    }

    if (preg_match('/^api\/monetor\/update_Weekly_Schedule_for_student\/(\d+)$/', $path, $matches)) {
        $teacherId = $matches[1];
        $teacher = \App\Models\Teacher::find($teacherId);
        return $teacher ? 'تم تعديل برنامج دوام المدرس: ' . $teacher->user->first_name . ' ' . $teacher->user->last_name : 'تم تعديل برنامج لأستاذ غير معروف';
    }

    if (preg_match('/^api\/monetor\/add_publish$/', $path)) {
        return 'تم إضافة إعلان جديد';
    }

    if (preg_match('/^api\/monetor\/delete_publish\/(\d+)$/', $path, $matches)) {
        $publishId = $matches[1];
        return 'تم حذف الإعلان رقم: ' . $publishId;
    }

    if (preg_match('/^api\/monetor\/update_publish\/(\d+)$/', $path, $matches)) {
        $publishId = $matches[1];
        return 'تم تعديل الإعلان رقم: ' . $publishId;
    }

    if (preg_match('/^api\/monetor\/add_mark_to_student\/(\d+)$/', $path, $matches)) {
        $studentId = $matches[1];
        $student = \App\Models\Student::find($studentId);
        return $student ? 'تم إضافة علامة للطالب: ' . $student->user->first_name . ' ' . $student->user->last_name : 'تم إضافة علامة لطالب غير معروف';
    }

    if (preg_match('/^api\/monetor\/edit_mark_for_student\/(\d+)\/subject\/(\d+)$/', $path, $matches)) {
        $studentId = $matches[1];
        $subjectId = $matches[2];
        $student = \App\Models\Student::find($studentId);
        return $student ? 'تم تعديل علامة الطالب: ' . $student->user->first_name . ' ' . $student->user->last_name . ' في المادة رقم: ' . $subjectId : 'تم تعديل علامة لطالب غير معروف';
    }

    if (preg_match('/^api\/monetor\/add_student_out_of_work\/(\d+)$/', $path, $matches)) {
        $studentId = $matches[1];
        $student = \App\Models\Student::find($studentId);
        return $student ? 'تم إضافة غياب للطالب: ' . $student->user->first_name . ' ' . $student->user->last_name : 'تم إضافة غياب لطالب غير معروف';
    }

    
    //تم تجريبه
    //تغيير حالة مناقشة مفعلة مغلقة
    if (preg_match('/^api\/monetor\/off_on_post\/(\d+)$/', $path, $matches)) {
        $postId = $matches[1];
        $post = \App\Models\Post::find($postId);
    
        if ($post) {
            // تحديد النص بناءً على قيمة state_on_off
            $stateText = $post->state_on_off == 1 ? 'مغلقة' : 'مفتوحة';
            return 'تم تغيير حالة المناقشة: ' . $post->quostion . ' إلى ' . $stateText;
        } else {
            return 'تم تغيير حالة المناقشة رقم: ' . $postId;
        }
    }

    //تم تجريبه
    //رفع برنامج لشعبة محددة لصف معين
    if (preg_match('/^api\/monetor\/upload_program_section\/(\d+)$/', $path, $matches)) {
        $sectionId = $matches[1];
        $section = \App\Models\Section::find($sectionId);
        return ' تم رفع برنامج للشعبة : ' . $section->num_section . ' الصف ' . $section->classs->name;
    }

    //تم تجريبه
    //حذف برنامج لشعبة محددة لصف معين    
    if (preg_match('/^api\/monetor\/delete_program_section\/(\d+)$/', $path, $matches)) {
        $programId = $matches[1];
        $program = \App\Models\Program_Student::find($programId);
        return 'تم حذف برنامج الشعبة ' . $program->section->num_section . ' الصف ' . $program->section->classs->name;
    }

    //تم تجريبه
    //تعديل برنامج شعبة محددة لصف معين
    if (preg_match('/^api\/monetor\/update_program_section\/(\d+)$/', $path, $matches)) {
        $programId = $matches[1];
        $program = \App\Models\Program_Student::find($programId);
        return 'تم تعديل برنامج الشعبة ' . $program->section->num_section . ' الصف ' . $program->section->classs->name;
    }

    //تم تجريبه
    //تعديل معلومات الموظف
    if (preg_match('/^api\/monetor\/update_profile_employee\/(\d+)$/', $path, $matches)) {
        $employeeId = $matches[1];
        $employee = \App\Models\Employee::find($employeeId);
        return $employee ? 'تم تعديل معلومات الموظف ' . $employee->first_name . ' ' . $employee->last_name : 'تم تعديل معلومات موظف غير معروف';
    }

    if (preg_match('/^api\/monetor\/update_teacher_profile\/(\d+)$/', $path, $matches)) {
        $teacherId = $matches[1];
        $teacher = \App\Models\Teacher::find($teacherId);
        return $teacher ? 'تم تعديل معلومات المدرس: ' . $teacher->user->first_name . ' ' . $teacher->user->last_name : 'تم تعديل معلومات مدرس غير معروف';
    }

    // بقية الحالات
    switch ($path) {
        case 'api/monetor/add_publish':
            return 'تم إضافة إعلان جديد';
        case 'api/monetor/delete_publish/{publish_id}':
            return 'تم حذف إعلان';
        default:
            return 'قام المستخدم بطلب ' . $path;
    }
}



}
