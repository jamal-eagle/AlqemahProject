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
        Schema::create('out__of__work__employees', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->integer('num_hour_out');
            $table->string('note');
            $table->unsignedBigInteger('school__mentor_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('acounting_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('out__of__work__employees');
    }
};
