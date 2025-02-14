<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('pre_enrollments');
        Schema::create('pre_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('period_id');
            $table->timestamps();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('period_id')->references('id')->on('periods');
        });


        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
