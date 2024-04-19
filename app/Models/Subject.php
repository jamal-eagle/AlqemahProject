<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Homework;
use App\Models\Course;
use App\Models\Archive;
use App\Models\Post;

class Subject extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'num_hour',
        'success_rate',
        'class_id'
    ];

    public function homework()
    {
        return $this->hasMany('App\Models\Homework',foreignKey:'subject_id',localKey:'id');
    }

    public function course()
    {
        return $this->hasMany('App\Models\Course',foreignKey:'subject_id',localKey:'id');
    }

    public function archive()
    {
        return $this->hasMany('App\Models\Archive',foreignKey:'subject_id',localKey:'id');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post',foreignKey:'subject_id',localKey:'id');
    }

    protected $hidden = [

    ];

}
