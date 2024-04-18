<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Out_Of_Work_Employee extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'date',
        'num_hour_out',
        'note',
        'school__mentor_id',
        'employee_id',
        'acounting_id',
        'teacher_id',
    ];
}
