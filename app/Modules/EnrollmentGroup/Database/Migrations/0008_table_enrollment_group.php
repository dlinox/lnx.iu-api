<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('enrollment_groups');
        Schema::create('enrollment_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('period_id');
            $table->unsignedBigInteger('created_by')->nullable(); // ID del usuario que creó la matrícula
            $table->enum('enrollment_modality', ['PRESENCIAL', 'VIRTUAL'])->default('PRESENCIAL'); // Modalidad del grupo
            $table->enum('status', ['MATRICULADO', 'RESERVADO', 'RETIRADO', 'EXPULSADO', 'CANCELADO'])->default('MATRICULADO');
            $table->boolean('special_enrollment')->default(false); // Indica si es una matrícula especial
            $table->boolean('with_enrollment')->default(false); // Indica si el estudiante tiene matrícula previa
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('period_id')->references('id')->on('periods');
        });


        // $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        // DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_groups');
    }
};
