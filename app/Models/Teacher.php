<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Course;
use App\Models\Post;
use App\Models\Comment;

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

    public function course()
    {
        return $this->hasMany('App\Models\Course',foreignKey:'teacher_id',localKey:'id');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post',foreignKey:'teacher_id',localKey:'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment',foreignKey:'teacher_id',localKey:'id');
    }

}
