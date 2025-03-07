<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('enrollments');

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('curriculum_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('module_id');
            // $table->unsignedBigInteger('payment_id')->nullable();
            $table->timestamps();
            // $table->foreign('curriculum_id')->references('id')->on('curriculums');
            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('module_id')->references('id')->on('modules');
            // $table->foreign('payment_id')->references('id')->on('payments');
        });


        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
