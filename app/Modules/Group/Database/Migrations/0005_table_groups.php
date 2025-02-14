<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {


        Schema::create('groups', function (Blueprint $table) {

            $table->id();
            $table->char('name', 10);
            $table->unsignedBigInteger('period_id');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->unsignedBigInteger('laboratory_id')->nullable();
            $table->unsignedBigInteger('curriculum_course_id');
            $table->integer('min_students')->default(0);
            $table->enum('modality', ['PRESENCIAL', 'VIRTUAL', 'MIXTO'])->default('PRESENCIAL');
            $table->string('observation', 255)->nullable();
            $table->enum('status', ['PENDIENTE', 'APERTURADO', 'FINALIZADO', 'CANCELADO'])->default('FINALIZADO');
            $table->boolean('is_enabled')->default(true);
            $table->foreign('period_id')->references('id')->on('periods');
            $table->foreign('teacher_id')->references('id')->on('teachers');
            $table->foreign('laboratory_id')->references('id')->on('laboratories');
            $table->foreign('curriculum_course_id')->references('id')->on('curriculum_courses');
            $table->timestamps();
        });

        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
