<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Out_Of_Work_Employee;
use App\Models\User;

class Acounting extends Model
{
    use HasFactory,Notifiable,HasApiTokens;
    protected $fillable = [
        'user_id'
    ];
public function out_of_work_employee()
{
    return $this->hasMany('App\Models\Out_Of_Work_Employee',foreignKey:'acounting_id',localKey:'id');
}

public function user(){
    return $this->belongsTo('App\Models\User',foreignKey:'user_id');
}

}
