<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Teacher extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'num_hour',
        'cost_hour',
        'num_hour_added',
        'note_hour_added',
        'user_id',
        'subject_id',
    ];
}
