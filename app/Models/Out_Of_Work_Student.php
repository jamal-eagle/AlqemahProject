<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Out_Of_Work_Student extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'date',
        'justification',
        'student_id',
    ];
}
