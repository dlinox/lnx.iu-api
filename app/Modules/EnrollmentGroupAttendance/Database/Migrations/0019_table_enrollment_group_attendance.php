<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('enrollment_group_attendances');
        Schema::create('enrollment_group_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_group_id')->nullable();
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['FALTA', 'TARDE', 'PRESENTE'])->default('PRESENTE');
            $table->string('observations')->nullable();
            $table->timestamps();
            $table->foreign('enrollment_group_id')->references('id')->on('enrollment_groups')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_group_attendances');
    }
};
