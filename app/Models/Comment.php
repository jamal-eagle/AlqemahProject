<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Comment extends Model
{
    use HasFactory,Notifiable,HasApiTokens;
    protected $fillable =[
        'description',
        'student_id',
        'post_id',
        'teacher_id',

    ];
}
