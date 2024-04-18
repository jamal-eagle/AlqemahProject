<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Student;
use App\Models\Homework;
use App\Models\Course;
use App\Models\Archive;

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

    public function homework()
    {
        return $this->hasMany('App\Models\Homework',foreignKey:'class_id',localKey:'id');
    }

    public function course()
    {
        return $this->hasMany('App\Models\Course',foreignKey:'class_id',localKey:'id');
    }

    public function archive()
    {
        return $this->hasMany('App\Models\Archive',foreignKey:'class_id',localKey:'id');
    }



}
