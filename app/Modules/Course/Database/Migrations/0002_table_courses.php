<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('courses');
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('code', 10)->nullable();
            $table->integer('hours_practice')->default(0);
            $table->integer('hours_theory')->default(0);
            $table->integer('credits')->default(0);
            $table->integer('order')->default(0);
            //cantidad unidades del curso
            $table->integer('units')->default(0);
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('curriculum_id');
            $table->unsignedBigInteger('pre_requisite_id')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->foreign('area_id')->references('id')->on('areas')->onDelete('restrict');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('restrict');
            $table->foreign('curriculum_id')->references('id')->on('curriculums')->onDelete('restrict');
            $table->foreign('pre_requisite_id')->references('id')->on('courses')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
