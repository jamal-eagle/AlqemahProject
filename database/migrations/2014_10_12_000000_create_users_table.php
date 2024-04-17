<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->primary();
            $table->varchar('first_name');
            $table->varchar('last_name');
            $table->varchar('father_name');
            $table->varchar('mother_name');
            $table->date('birthday');
            $table->tinyInteger('gender');
            $table->varchar('phone');
            $table->varchar('address');
            $table->varchar('year');
            $table->varchar('image')->nullable();
            $table->varchar('email')->unique();
            $table->varchar('password');
            $table->varchar('conf_password');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
