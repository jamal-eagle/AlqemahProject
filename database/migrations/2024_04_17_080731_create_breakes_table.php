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
        Schema::create('breakes', function (Blueprint $table) {
            $table->id();
            $table->varchar('first_name');
            $table->varchar('last_name');
            $table->varchar('phone');
            $table->varchar('address');
            $table->varchar('year');
            $table->double('cost_from_breake');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakes');
    }
};
