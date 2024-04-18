<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Expenses extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'date',
        'product',
        'cost_one_piece',
        'num_product',
        'total_cost',
        'year',
    ];
}
