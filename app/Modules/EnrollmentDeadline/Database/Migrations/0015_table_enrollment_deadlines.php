<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('enrollment_deadlines');
        Schema::create('enrollment_deadlines', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->text('observations')->nullable();
            $table->enum('type', ['REGULAR', 'AMPLIACION'])->default('REGULAR');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedBigInteger('period_id');
            $table->timestamps();
            $table->foreign('reference_id')->references('id')->on('enrollment_deadlines');
            $table->foreign('period_id')->references('id')->on('periods');
            $table->index('type');
            $table->index('period_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_deadlines');
    }
};
