<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
class Salary extends Model
{
    use HasFactory;
    protected $fillable = [
        'salary_of_teacher',
        'month',
        'teacher_id',
    ];
    protected $timestamp = true;

    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher',foreignKey:'teacher_id');
    }
}
