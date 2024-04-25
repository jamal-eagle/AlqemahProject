<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\User;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Parentt extends Model
{
    use HasFactory ,Notifiable,HasApiTokens;

    protected $fillable = [
        'user_id',
    ];

    public function student()
    {
        return $this->hasMany('App\Models\Student',foreignKey:'parentt_id',localKey:'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User',foreignKey:'user_id');
    }

}
