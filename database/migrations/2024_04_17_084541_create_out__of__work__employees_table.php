<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\School_Mentor;
use App\Models\Employee;
use App\Models\Acounting;
use App\Models\Teacher;

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
            $table->foreignIdFor(School_Mentor::class,'school__mentor_id')->nullable();
            $table->foreignIdFor(Employee::class,'employee_id')->nullable();
            $table->foreignIdFor(Acounting::class,'acounting_id')->nullable();
            $table->foreignIdFor(Teacher::class,'teacher_id')->nullable();

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
