<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('academic_records');
        Schema::create('academic_records', function (Blueprint $table) {
            $table->id();
            $table->json('payload')->nullable();
            $table->text('observations')->nullable();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('grade_deadline_id')->nullable();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('grade_deadline_id')->references('id')->on('grade_deadlines');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_records');
    }
};
