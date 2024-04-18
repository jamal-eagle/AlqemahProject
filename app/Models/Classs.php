<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Student;

class Classs extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'fee_school_id',
    ];

    public function student()
    {
        return $this->hasMany('App\Models\Student',foreignKey:'class_id',localKey:'id');
    }

}
