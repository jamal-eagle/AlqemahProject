<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Fee_School extends Model
{
    use HasFactory,Notifiable,HasApiTokens;
    protected $fillable = [
        'year',
        'amount',
        'class_id',
    ];
}
