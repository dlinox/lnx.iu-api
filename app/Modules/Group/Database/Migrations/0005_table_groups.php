<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('groups');
        Schema::create('groups', function (Blueprint $table) {

            $table->id();
            $table->char('name', 10);
            $table->unsignedBigInteger('period_id');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->unsignedBigInteger('laboratory_id')->nullable();
            $table->unsignedBigInteger('course_id');
            $table->integer('min_students')->default(0);
            $table->integer('max_students')->default(0);
            $table->enum('modality', ['PRESENCIAL', 'VIRTUAL',])->default('PRESENCIAL');
            $table->string('observation', 255)->nullable();
            $table->enum('status', ['ABIERTO', 'CERRADO', 'CANCELADO', 'FINALIZADO'])->default('FINALIZADO');
            $table->timestamps();
            $table->foreign('period_id')->references('id')->on('periods');
            $table->foreign('teacher_id')->references('id')->on('teachers');
            $table->foreign('laboratory_id')->references('id')->on('laboratories');
            $table->foreign('course_id')->references('id')->on('courses');
        });

        // $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        // DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
