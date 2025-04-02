<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //drop table if exists
        Schema::dropIfExists('pre_registers');
        Schema::create('pre_registers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('token');
            $table->string('student_type')->nullable();
            $table->string('student_code')->nullable();
            $table->boolean('email_verified')->default(0);
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_registers');
    }
};
