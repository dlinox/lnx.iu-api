<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->unique();
            $table->char('short_name', 3)->unique();
        });

        Schema::create('months', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->unique();
            $table->char('short_name', 3)->unique();
        });

        Schema::create('genders', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->unique();
            $table->char('short_name', 4)->unique();
            $table->boolean('is_enabled')->default(true);
        });
        //locations
        //contries
    }

    public function down(): void
    {
        Schema::dropIfExists('days');
        Schema::dropIfExists('months');
        Schema::dropIfExists('genders');
    }
};
