<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Student;

class Pay_Fee extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'date',
        'amount_money',
        'remaining_fee',
        'student_id',
    ];

    public function student(){
        return $this->belongsTo('App\Models\Student',foreignKey:'student_id');
    }
}
