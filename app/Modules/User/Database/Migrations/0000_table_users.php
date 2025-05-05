<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username');
            $table->string('email');
            $table->enum('model_type', ['admin', 'teacher', 'student']);
            $table->unsignedBigInteger('model_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            //indexes
            $table->unique(['username', 'model_type']);
            $table->unique(['email', 'model_type']);
            $table->index('model_type');
            $table->index('model_id');
            $table->index('name');
            $table->index('username');
            $table->index('email');
            //fulltext indexes name
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
