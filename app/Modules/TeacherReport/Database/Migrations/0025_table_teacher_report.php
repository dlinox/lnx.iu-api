<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('period_id');
            $table->integer('number');
            $table->json('payload');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
            $table->unique(['teacher_id', 'period_id', 'number'], 'teacher_reports_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_reports');
    }
};
