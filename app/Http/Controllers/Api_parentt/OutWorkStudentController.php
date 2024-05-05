<?php

namespace App\Http\Controllers\Api_parentt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Out_Of_Work_Student;

class OutWorkStudentController extends Controller
{
    //عرض كل غيابات الابن
    public function all_out_work_student($student_id)
    {
        $Out_Of_Work_Student = Out_Of_Work_Student::where('student_id',$student_id)->get();
        return $Out_Of_Work_Student;
    }

    //إضافة تبرير للابن لغيابه في يوم محدد
    public function add_Justification(Request $request, $Out_Of_Work_Student_id)
    {
        $Out_Of_Work_Student = Out_Of_Work_Student::where('id', $Out_Of_Work_Student_id)->first();

        if ($Out_Of_Work_Student) {
            $Out_Of_Work_Student->justification = $request->justification;
            $Out_Of_Work_Student->save();
            
            return response()->json(['message' => 'Justification updated successfully'], 200);
        } else {
            return response()->json(['message' => 'Out of Work Student not found'], 404);
        } 
        
    }


}
