<?php

use App\Http\Middleware\chech_Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

route::get('/p',function(){
    return 'i like this';
})->middleware(['auth','chech_admin']);
