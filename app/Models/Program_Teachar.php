<?php

namespace App\Models;

use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Teacher;

class Program_Teachar extends Model
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $fillable = [
        'teacher_id'
    ];
    public $Timestamp = false;

    public function teachar(){
        return $this->belongsTo('App\Models\Teacher',foreignKey:'teacher_id');
    }
}
