<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_module_courses', function (Blueprint $table) {
            $table->id();
            $table->integer('order');
            $table->char('code', 10)->nullable();
            $table->integer('hours_practice')->nullable();
            $table->integer('hours_theory')->nullable();
            $table->integer('credits')->nullable();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('curriculum_module_id');
            $table->unsignedBigInteger('pre_requisite_id')->nullable();
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('curriculum_module_id')->references('id')->on('curriculum_modules');
            $table->foreign('pre_requisite_id')->references('id')->on('curriculum_module_courses');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_module_courses');
    }
};
