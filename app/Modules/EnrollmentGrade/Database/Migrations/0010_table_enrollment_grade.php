<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('enrollment_grades');
        Schema::create('enrollment_grades', function (Blueprint $table) {
            $table->id();
            $table->decimal('final_grade', 5, 2)->default(0);
            $table->decimal('capacity_average', 5, 2)->default(0);
            $table->enum('attitude_grade', ['A', 'B', 'C'])->default('A');
            $table->unsignedBigInteger('enrollment_group_id');
            $table->foreign('enrollment_group_id')->references('id')->on('enrollment_groups');
            $table->timestamps();
        });
        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_grades');
    }
};
