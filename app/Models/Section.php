<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Student;
use App\Models\Post;
use App\Models\Program_Student;

class Section extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'num_section',
        'class_id',
    ];

    public function student()
    {
        return $this->hasMany('App\Models\Student',foreignKey:'section_id',localKey:'id');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post',foreignKey:'section_id',localKey:'id');
    }

    public function program_student(){
        return $this->hasMany('App\Models\Program_Student',foreignKey:'section_id',localKey:'id');
    }

}
