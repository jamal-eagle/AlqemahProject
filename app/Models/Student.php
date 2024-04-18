<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;
use App\Models\Classs;
use App\Models\Section;
use App\Models\Parentt;
class Student extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'calssification',
        'school_tuition',
        'user_id',
        'class_id',
        'section_id',
        'parentt_id',
    ];
    public function user(){
        return $this->belongsTo('App\Models\User',foreignKey:'user_id');
    }

    public function classs(){
        return $this->belongsTo('App\Models\Classs',foreignKey:'class_id');
    }
    public function section(){
        return $this->belongsTo('App\Models\Section',foreignKey:'section_id');
    }

    public function parentt(){
        return $this->belongsTo('App\Models\Parentt',foreignKey:'parentt_id');
    }

}
