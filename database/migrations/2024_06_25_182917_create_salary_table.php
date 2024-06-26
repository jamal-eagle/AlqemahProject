<?php

use App\Models\Teacher;
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
        Schema::create('salary', function (Blueprint $table) {
            $table->id();
            $table->double('salary_of_teacher');
            $table->date('month');
            $table->foreignIdFor(Teacher::class,'teacher_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary');
    }
};
