<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Actions_log; // استدعاء موديل ActionLog لتسجيل الأنشطة
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            $description = $this->getActionDescription($request->path(), $request);

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


private function getActionDescription($path, $request)
{
    //تم تجريبه
    // تحديث معلومات طالب
    if (preg_match('/^api\/monetor\/update_profile_student\/(\d+)$/', $path, $matches)) {
        // استخراج معرف الطالب من الـ path
        $studentId = $matches[1];

        // جلب بيانات الطالب عبر العلاقة بين User و Student
        $student = \App\Models\Student::find($studentId);
        $data = $request->input();
        // إذا كان الطالب موجودًا، جلب المستخدم المرتبط بالطالب وعرض اسمه
        if ($student && $student->user) {
            $user = $student->user;
            $newClass = \App\Models\Classs::find($data['class_id']);
            $newSection = \App\Models\Section::find($data['section_id']);
            return 'تم تحديث معلومات الطالب: ' . $user->first_name .' ' .$user->father_name.  ' ' . $user->last_name . ' ' . $user->mother_name . ' ' .$user->phone . ' ' . $user->address . ' ' . $user->school_tuition. ' '.$student->classs->name .' '.$student->section->num_section   .'\n ((إلى)) \n' . $data['first_name'] . ' ' . $data['father_name'] . ' ' . $data['last_name'] . ' ' . $data['mother_name'] . ' ' . $data['phone'] . ' ' . $data['address'] . ' ' . $data['school_tuition'] . ' ' . ($newClass ? $newClass->name : 'غير معروف') . ' ' . ($newSection ? $newSection->num_section : 'غير معروف');
        } else {
            return 'تم تحديث معلومات طالب غير معروف (ID: ' . $studentId . ')';
        }
    }

    //تم تجريبه
    // إرسال إنذار أو ثناء ...لطالب
    // if (preg_match('/^api\/monetor\/create_note\/(\d+)$/', $path, $matches)) {
    //     $studentId = $matches[1];
    //     $student = \App\Models\Student::find($studentId);
    //     return $student ? 'تم إرسال ملاحظة /إنذار للطالب: ' . $student->user->first_name . ' ' . $student->user->last_name : 'تم إرسال ملاحظة لطالب غير معروف';
    // }
    if (preg_match('/^api\/monetor\/create_note\/(\d+)$/', $path, $matches)) {
        $studentId = $matches[1];
        $student = \App\Models\Student::find($studentId);
    
        // التحقق من وجود الطالب
        if (!$student) {
            return 'تم إرسال ملاحظة لطالب غير معروف';
        }
    
        // الحصول على البيانات المرسلة
        $data = $request->input(); // الحصول على البيانات المرسلة عبر الـ API
    
        // عرض التفاصيل المخزنة
        $details = '';
        if (isset($data['note_details']) && is_array($data['note_details'])) {
            foreach ($data['note_details'] as $detail) {
                $details .= ' [' . $detail['key'] . ': ' . $detail['value'] . ']';
            }
        }
    
        // عرض النتيجة النهائية
        return 'تم إرسال ' . ($data['type'] ?? 'ملاحظة') . ' للطالب: ' 
            . $student->user->first_name . ' ' . $student->user->last_name 
            . ' بالتفاصيل التالية: ' . ($data['text'] ?? ' ');
    }

    //تم تجربته
    //تعديل أو إضافة برنامج مدرس
    if (preg_match('/^api\/monetor\/update_Weekly_Schedule_for_student\/(\d+)$/', $path, $matches)) {
        $teacherId = $matches[1];
        $teacher = \App\Models\Teacher::find($teacherId);
        return $teacher ? 'تم تعديل برنامج دوام المدرس: ' . $teacher->user->first_name . ' ' . $teacher->user->last_name : 'تم تعديل برنامج لأستاذ غير معروف';
    }

    //تم تجربته
    //إضافة إعلان
    if (preg_match('/^api\/monetor\/add_publish$/', $path)) {
        $data = $request->input();
        if ($request->path) {
            return 'تم إضافة إعلان جديد و هو ' . $data['description'] . ' مع إرفاق صورة للإعلان ';
        }
        return 'تم إضافة إعلان جديد و هو ' . $data['description'];
    }

    //تم تجربته
    //حذف إعلان
    if (preg_match('/^api\/monetor\/delete_publish\/(\d+)$/', $path, $matches)) {
        $publishId = $matches[1];
        $publish = \App\Models\Publish::find($publishId);
        if ($publish->course_id == null) {
            return 'تم حذف الإعلان : ' . $publish->description;
        }
        $course = \App\Models\Course::find($publish->course_id);

        return 'تم حذف الإعلان : ' . $publish->description . ' للدورة '. $course->name_course;
    }
        // }
        

    //تم تجربته   
    //تعديل إعلان
    if (preg_match('/^api\/monetor\/update_publish\/(\d+)$/', $path, $matches)) {
        $publishId = $matches[1];
        $publish = \App\Models\Publish::find($publishId);
        $data = $request->input();
        if ($publish->course_id == null) {
            if ($request->path && $request->description) {
                return 'تم تعديل الإعلان : ' . $publish->description . ' إلى ' . $data['description'] . ' و قد تم تعديل صورة ';
            }
            if ($request->path) {
                return 'تم تعديل الإعلان : ' . $publish->description . ' حيث تم تعديل صورة ';
            }
            if ($request->description) {
                return 'تم تعديل الإعلان : ' . $publish->description . ' إلى ' . $data['description'];
            }

             
        }
        $course = \App\Models\Course::find($publish->course_id);

        if ($request->path && $request->description) {
            return 'تم تعديل الإعلان : ' . $publish->description . ' للدورة '. $course->name_course . ' إلى ' . $data['description'] . ' و قد تم تعديل صورة ';
        }

        if ($request->path) {
            return 'تم تعديل الإعلان : ' . $publish->description . ' للدورة '. $course->name_course . ' حيث تم تعديل صورة ';
        }
        if ($request->description) {
            return 'تم تعديل الإعلان : ' . $publish->description . ' للدورة '. $course->name_course . ' إلى ' . $data['description'];
        }
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

    //تم تجريبه
    //إضافة غياب لطالب
    if (preg_match('/^api\/monetor\/add_student_out_of_work\/(\d+)$/', $path, $matches)) {
        $studentId = $matches[1];
        $student = \App\Models\Student::find($studentId);

        if ($request->justification) {
            return $student ? 'تم إضافة غياب للطالب: ' . $student->user->first_name . ' ' . $student->user->last_name . ' في اليوم ' . $request->date . ' و قد تم تبريره بالتالي ' . $request->justification : 'تم إضافة غياب لطالب غير معروف';
        }
        return $student ? 'تم إضافة غياب للطالب: ' . $student->user->first_name . ' ' . $student->user->last_name . ' في اليوم ' . $request->date : 'تم إضافة غياب لطالب غير معروف';
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
        $data = $request->input();
        return $employee ? 'تم تعديل معلومات الموظف ' . $employee->first_name . ' ' . $employee->last_name . ' ' .$employee->phone . ' ' . $employee->address . ' ' . $employee->salary . ' ' . $employee->type . '\n ((إلى)) \n' . $data['first_name'] . ' ' . 
        $data['last_name'] . ' ' . $data['phone'] . ' ' . $data['address'] . ' ' . $data['salary'] . ' ' . $data['type'] : 'تم تعديل معلومات موظف غير معروف';
    
    }

    //تم تجربته
    //تسجيل طالب خارجي في كورس
    if (preg_match('/^api\/monetor\/add-order-course\/(\d+)$/', $path, $matches)) {
        $data = $request->input();

        $courseId = $matches[1];
    
        $course = \App\Models\Course::find($courseId);

        if ($course) {
            return 'تم تسجيل الطالب ' .$data['first_name'] . ' '.$data['last_name'] .' ابن '. $data['father_name'] . ' في دورة '. $course->name_course;
        } else {
            return 'تم تسجيل طالب في دورة غير معروفة (ID: ' . $courseId . ')';
        }
    }
    

    if (preg_match('/^api\/monetor\/update_teacher_profile\/(\d+)$/', $path, $matches)) {
        $teacherId = $matches[1];
        $teacher = \App\Models\Teacher::find($teacherId);
        return $teacher ? 'تم تعديل معلومات المدرس: ' . $teacher->user->first_name . ' ' . $teacher->user->last_name : 'تم تعديل معلومات مدرس غير معروف';
    }

    //تم تجربته
    //رفع مرفق لدورة محددة
    if (preg_match('/^api\/monetor\/upload_file_image_for_course\/(\d+)$/', $path, $matches)) {
        $courseId = $matches[1];
    
        $course = \App\Models\Course::find($courseId);
    
        if ($course) {
            if ($request->description) {
                return 'تم رفع مرفق للدورة: ' . $course->name_course . ' بالوصف التالي ' . $request->description;
            }
            else {
                return 'تم رفع مرفق للدورة: ' . $course->name_course . ' دون وصف ';
            }
        } else {
            return 'تم رفع مرفق لدورة غير معروفة (ID: ' . $courseId . ')';
        }
    }

    //تم تجربته
    //رفع مرفق لعام دراسي الحالي
    if (preg_match('/^api\/monetor\/upload_file_image\/(\d+)$/', $path, $matches)) {
        $academy = \App\Models\Academy::find('1');
        $subjectId = $matches[1];
    
        $subject = \App\Models\Subject::find($subjectId);
    
        if ($subject) {
                return 'تم رفع مرفق لمادة: ' . $subject->name . ' للصف ' . $subject->classs->name .' للعام الدراسي الحالي '.$academy->year . ' بالوصف التالي ' . $request->description;
        } else {
            return 'تم رفع مرفق للعام الدراسي الحالي غير معروفة (ID: ' . $courseId . ')';
        }
    }

    //تم تجربته
    //رفع مرفق لعام دراسي محدد أي أرشيف
    if (preg_match('/^api\/monetor\/upload_file_image_archive\/(\d+)$/', $path, $matches)) {
        // استخراج معرف الأرشيف من الـ path
        $archiveId = $matches[1];
    
        // جلب بيانات الأرشيف عبر معرف الأرشيف
        $archive = \App\Models\Archive::find($archiveId);
    
        // إذا كان الأرشيف موجودًا، يمكن عرض تفاصيله أو عرض رسالة عند عدم وجود الأرشيف
        if ($archive) {
            return 'تم رفع مرفق للأرشيف : ' . $archive->year . ' للمادة: ' . $archive->subject->name . ' للصف: ' . $archive->class->name . ' بالوصف التالي ' . $request->description;
        } else {
            return 'تم رفع مرفق لأرشيف غير معروف (ID: ' . $archiveId . ')';
        }
    }
    

    //تم تجربته
    //الموافقة على طلب التسجيل لطالب في كورس
    if (preg_match('/^api\/monetor\/ok_order_course\/(\d+)$/', $path, $matches)) {
        $orderId = $matches[1];
    
        $order = \App\Models\Order::find($orderId);
    
        if ($order) {
            return 'تمت الموافقة على طلب التسجيل للطالب: ' . $order->student->user->first_name . ' ' . $order->student->user->last_name . ' في الدورة ' . $order->course->name_course;
        } else {
            return 'تمت الموافقة على طلب التسجيل غير معروف (ID: ' . $orderId . ')';
        }
    }
    
    //تم تجربته
    //تم رفض طلب التسجيل لطالب في كورس
    if (preg_match('/^api\/monetor\/no_order_course\/(\d+)$/', $path, $matches)) {
        $orderId = $matches[1];
    
        $order = \App\Models\Order::find($orderId);
    
        if ($order) {
            return 'تمت رفض طلب التسجيل للطالب: ' . $order->student->user->first_name . ' ' . $order->student->user->last_name . ' في الدورة ' . $order->course->name_course;
        } else {
            return 'تمت رفض طلب التسجيل غير معروف (ID: ' . $orderId . ')';
        }
    }

    //تم تجربته
    //حذف ملف لمادة 
    if (preg_match('/^api\/monetor\/delete_file_image\/(\d+)\/(.+)$/', $path, $matches)) {
        $fileImgId = $matches[1];
    
        $file = \App\Models\File_Archive::find($fileImgId);
    
        if ($file) {
                return 'تم حذف المرفق: ' . $file->description . ' للمادة ' . $file->archive->subject->name . ' الصف ' . $file->archive->class->name . ' من العام الدراسي ' . $file->archive->year;
           
        } else {
            return 'تم حذف مرفق غير معروف (ID: ' . $fileImgId . ')';
        }
    }

    // if (preg_match('/^api\/monetor\/update-file-image\/(\d+)$/', $path, $matches)) {
    //     $fileImgId = $matches[1];
    
    //     $file = \App\Models\File_Archive::find($fileImgId);
    
    //     if ($file) {
    //         $data = $request->all();

    //         if ($request->description) {
    //             return 'تم تحديث المرفق: ' . $file->description . ' (  ' . ', الوصف السابق: ' . $file->description  . ', الوصف الجديد: ' . $data['description'] . ') للمادة ' . $file->archive->subject->name . ' الصف ' . $file->archive->class->name . ' من العام الدراسي ' . $file->archive->year;
    //         }
    
    //         return 'تم تحديث المرفق: ' . $file->description  . ') للمادة ' . $file->archive->subject->name . ' الصف ' . $file->archive->class->name . ' من العام الدراسي ' . $file->archive->year;
    //     } else {
    //         return 'تم تحديث مرفق غير معروف (ID: ' . $fileImgId . ')';
    //     }
    // }

    //تم تجريبه
    //حذف تعليق لطالب
    if (preg_match('/^api\/monetor\/delete_comment\/(\d+)$/', $path, $matches)) {
        $commentId = $matches[1];
    
        // جلب بيانات التعليق عبر معرف التعليق
        $comment = \App\Models\Comment::find($commentId);
    
        if ($comment) {
            // عرض تفاصيل التعليق المحذوف
            return 'تم حذف التعليق: "' . $comment->description . '" الذي كتبه ' . $comment->student->user->first_name . ' ' . $comment->student->user->last_name . ' في المناقشة ذات العنوان التالي ' . $comment->post->quostion;
        } else {
            return 'تم حذف تعليق غير معروف (ID: ' . $commentId . ')';
        }
    }

    //تم تجريبه
    //تعديل تبرير غياب طالب
    if (preg_match('/^api\/monetor\/updateAbsence_for_student\/(\d+)\/(.+)$/', $path, $matches)) {
        $studentId = $matches[1];
        $date = $matches[2];
    
        $student = \App\Models\Student::find($studentId);
        $absence = \App\Models\Out_Of_Work_Student::where('student_id', $studentId)->where('date', $date)->first();
        return 'تم تعديل تبرير غياب الطالب: ' . $student->user->first_name . ' ' . $student->user->last_name . ' من التبرير السابق: "' . $absence->justification . '" إلى التبرير الجديد: "' . $request->justification. '" للتاريخ: ' . $date;
    }
    
    //تم تجريبه
    //حذف تبرير غياب طالب
    if (preg_match('/^api\/monetor\/delete_student_out_of_work\/(\d+)\/(.+)$/', $path, $matches)) {
        $studentId = $matches[1];
        $date = $matches[2];
    
        $student = \App\Models\Student::find($studentId);
        $absence = \App\Models\Out_Of_Work_Student::where('student_id', $studentId)->where('date', $date)->first();
        return 'تم حذف سجل غياب الطالب: ' . $student->user->first_name . ' ' . $student->user->last_name . ' للتاريخ: ' . $date . ' مع التبرير: "' . $absence->justification . '"' ?? 'تم حذف سجل غياب لطالب غير معروف (ID: ' . $studentId . ')';
    }

    //تم تجربته
    //إضافة ساعات إضافية للأستاذ
    if (preg_match('/^api\/monetor\/add_extrahour\/(\d+)$/', $path, $matches)) {
        $teacherId = $matches[1]; 
        
        $teacher = \App\Models\Teacher::find($teacherId);

        if ($request->note_hour_added) {
            return 'تم إضافة '.$request->num_hour_added. ' ساعات إضافية تحت ملاحظة '. $request->note_hour_added. ' للأستاذ ' . $teacher->user->first_name . ' '. $teacher->user->last_name;
        }
        return 'تم إضافة '.$request->num_hour_added. ' ساعات إضافية ' . ' للأستاذ ' . $teacher->user->first_name . ' '. $teacher->user->last_name;

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
