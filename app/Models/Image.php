<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Publish;

class Image extends Model
{
    use HasFactory, Notifiable,HasApiTokens;
    protected $fillable = [
        'path',
        'description',
        'program_student_id',
        'program_teacher_id',
        'publish_id',

    ];

    public function student(){
        return $this->belongsTo('App\Models\Student',foreignKey:'program_student_id');
    }

    public function teacher(){
        return $this->belongsTo('App\Models\Teacher',foreignKey:'program_teacher_id');
    }

    public function publish(){
        return $this->belongsTo('App\Models\Publish',foreignKey:'publish_id');
    }
}
