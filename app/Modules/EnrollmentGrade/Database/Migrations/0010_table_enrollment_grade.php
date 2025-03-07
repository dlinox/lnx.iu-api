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
            $table->decimal('grade', 5, 2)->default(0);
            $table->unsignedBigInteger('enrollment_group_id');
            //si ya no se puede modificar la nota
            $table->boolean('is_locked')->default(true);
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
