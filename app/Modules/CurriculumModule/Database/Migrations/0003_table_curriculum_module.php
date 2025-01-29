<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_modules', function (Blueprint $table) {
            $table->id();
            $table->integer('order');
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('module_id')->nullable();
            $table->unsignedBigInteger('curriculum_id');
            $table->foreign('area_id')->references('id')->on('areas');
            $table->foreign('module_id')->references('id')->on('modules');
            $table->foreign('curriculum_id')->references('id')->on('curriculums');
            $table->boolean('is_extracurricular')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_modules');
    }
};
