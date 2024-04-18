<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Order extends Model
{
    use HasFactory,Notifiable,HasApiTokens;
    protected $fillable = [
        'first_name',
        'last_name',
        'father_name',
        'birthday',
        'gender',
        'phone',
        'address',
        'email',
        'classification',
        'class',
        'year',
        'studen_id',
        'course_id',

    ];
}
