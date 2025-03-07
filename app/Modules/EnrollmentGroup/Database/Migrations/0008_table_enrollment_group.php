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
            // $table->unsignedBigInteger('payment_id');
            $table->enum('status', ['ABIERTO', 'CERRADO', 'CANCELADO', 'FINALIZADO'])->default('FINALIZADO');
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('period_id')->references('id')->on('periods');
            // $table->foreign('payment_id')->references('id')->on('payments');
        });


        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_groups');
    }
};
