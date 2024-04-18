<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
}
