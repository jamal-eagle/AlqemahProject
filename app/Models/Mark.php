<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Mark extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'ponus',
        'homework',
        'oral',
        'test1',
        'test2',
        'exam_med',
        'exam_final',
        'state',
        'student_id',
        'subject_id',
    ];
}
