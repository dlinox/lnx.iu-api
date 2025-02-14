<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_courses', function (Blueprint $table) {
            $table->id();
            $table->char('code', 10)->nullable();
            $table->integer('order');
            $table->integer('hours_practice')->nullable();
            $table->integer('hours_theory')->nullable();
            $table->integer('credits')->default(0);
            $table->unsignedBigInteger('pre_requisite_id')->nullable();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('module_id')->nullable();
            $table->unsignedBigInteger('curriculum_id');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('curriculum_id')->references('id')->on('curriculums');
            $table->foreign('module_id')->references('id')->on('modules');
            $table->foreign('area_id')->references('id')->on('areas');
            $table->foreign('pre_requisite_id')->references('id')->on('curriculum_courses');
            $table->boolean('is_extracurricular')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_courses');
    }
};
