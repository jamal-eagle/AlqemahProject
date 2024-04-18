<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Publish;
use App\Models\Subject;
use App\Models\Classs;
use App\Models\Teacher;

class Course extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name_course',
        'description',
        'cost_course',
        'start_date',
        'finish_date',
        'start_time',
        'finish_time',
        'year',
        //'publish_id',
        'subject_id',
        'class_id',
        'teacher_id',
    ];

    public function publish ()
    {
        return $this->hasOne('App\Models\Publish',foreignKey:'course_id');
    }

    public function subject(){
        return $this->belongsTo('App\Models\Subject',foreignKey:'subject_id');
    }

    public function classs(){
        return $this->belongsTo('App\Models\Classs',foreignKey:'class_id');
    }

    public function teacher(){
        return $this->belongsTo('App\Models\Teacher',foreignKey:'teacher_id');
    }
}
