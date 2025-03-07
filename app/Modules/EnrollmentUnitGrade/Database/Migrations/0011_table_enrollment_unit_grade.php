<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('enrollment_unit_grades');
        Schema::create('enrollment_unit_grades', function (Blueprint $table) {
            $table->id();
            $table->decimal('grade', 5, 2)->default(0);
            $table->integer('order')->default(1);
            $table->unsignedBigInteger('enrollment_grade_id');
            $table->timestamps();
            $table->foreign('enrollment_grade_id')->references('id')->on('enrollment_grades')->onDelete('cascade');
        });
        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_unit_grades');
    }
};
