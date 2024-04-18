<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Homework extends Model
{
    use HasFactory,Notifiable,HasApiTokens;
    protected $fillable = [
        'description',
        'year',
        'class_id',
        'subject_id',
        
    ];
}
