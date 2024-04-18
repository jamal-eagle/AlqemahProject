<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
        'publish_id',
        'subject_id',
        'class_id',
        'teacher_id',
    ];
}
