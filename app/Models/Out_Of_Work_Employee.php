<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\School_Mentor;
use App\Models\Employee;
use App\Models\Acounting;
use App\Models\Teacher;

class Out_Of_Work_Employee extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'date',
        'num_hour_out',
        'note',
        'school__mentor_id',
        'employee_id',
        'acounting_id',
        'teacher_id',
    ];

    public function school__mentor(){
        return $this->belongsTo('App\Models\School_Mentor',foreignKey:'school__mentor_id');
    }

    public function employee(){

        return $this->belongsTo('App\Models\Employee',foreignKey:'employee_id');

    }
    public function acounting(){
        return $this->belongsTo('App\Models\Acounting',foreignKey:'acounting_id');
    }
    public function teacher(){
        return $this->belongsTo('App\Models\Teacher',foreignKey:'teacher_id');
    }
}
