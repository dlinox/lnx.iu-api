<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('student_type_id');
            $table->decimal('presential_price', 10, 2)->nullable();
            $table->decimal('virtual_price', 10, 2)->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->foreign('curriculum_id')->references('id')->on('curriculums');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('student_type_id')->references('id')->on('student_types');
        });

        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('course_prices');
    }
};
