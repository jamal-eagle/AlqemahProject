<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Student;
use App\Models\Course;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Program_Teachar;
use App\Models\Out_Of_Work_Employee;
use App\Models\User;
use App\Models\Subject;
use App\Models\Teacher_section;
use App\Models\Section;
use App\Models\Teacher_Schedule;
use App\Models\Maturitie;

class Teacher extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'cost_hour',
        'num_hour_added',
        'note_hour_added',
        'user_id',
        'course_id',
        'certificate',
        //'subject_id',
    ];

    public function course()
    {
        return $this->hasMany('App\Models\Course',foreignKey:'teacher_id',localKey:'id');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post',foreignKey:'teacher_id',localKey:'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment',foreignKey:'teacher_id',localKey:'id');
    }

    public function image(){
        return $this->hasMany('App\Models\Image',foreignKey:'program_teacher_id',localKey:'id');
    }

    public function program_teachar(){
        return $this->hasMany('App\Models\Program_Teachar',foreignKey:'teacher_id',localKey:'id');
    }

    public function out_of_work_employee()
{
    return $this->hasMany('App\Models\Out_Of_Work_Employee',foreignKey:'teacher_id',localKey:'id');
}

public function user(){
    return $this->belongsTo('App\Models\User',foreignKey:'user_id');
}
// public function subject(){
//     return $this->belongsTo('App\Models\Subject',foreignKey:'subject_id');
// }

// public function teacher_section()
//     {
//         return $this->hasMany('App\Models\Teacher_section',foreignKey:'teacher_id',localKey:'id');
//     }

public function sections()
{
    return $this->belongsToMany(Section::class, 'teacher_sections', 'section_id','teacher_id');
}

public function teacher_schedule()
{
        return $this->hasOne(Teacher_Schedule::class);
}


public function subject()
{
    return $this->belongsToMany(Subject::class,'teacher_subjects','teacher_id','subject_id');
}

public function salary()
    {
        return $this->hasMany('App\Models\Salary',foreignKey:'teacher_id',localKey:'id');
    }

public function maturitie()
    {
        return $this->hasMany('App\Models\Maturitie',foreignKey:'teacher_id',localKey:'id');
    }

}
