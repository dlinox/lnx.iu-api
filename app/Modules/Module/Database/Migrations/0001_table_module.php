<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->char('code', 10);
            $table->integer('level')->default(1);
            $table->unsignedBigInteger('curriculum_id');
            $table->boolean('is_extracurricular')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->foreign('curriculum_id')->references('id')->on('curriculums')->onDelete('restrict');
            $table->unique(['name', 'curriculum_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
