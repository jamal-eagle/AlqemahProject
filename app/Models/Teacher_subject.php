<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Teacher_subject extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'subject_id',
        'teacher_id',
    ];
}
