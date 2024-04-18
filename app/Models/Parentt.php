<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Student;

class Parentt extends Model
{
    use HasFactory,Notifiable,HasApiTokens;
    protected $fillable = [
        'user_id',

    ];

    public function student()
    {
        return $this->hasMany('App\Models\Student',foreignKey:'parentt_id',localKey:'id');
    }
}
