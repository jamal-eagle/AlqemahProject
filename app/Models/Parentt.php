<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Parentt extends Model
{
    use HasFactory,Notifiable,HasApiTokens;
    protected $fillable = [
        'user_id',

    ];
}
