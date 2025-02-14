<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // INSERT INTO enrollment_groups(id, pre_enrollment_id, payment_id) VALUES (1, 1, 2);
        Schema::dropIfExists('enrollment_groups');
        Schema::create('enrollment_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pre_enrollment_id');
            $table->unsignedBigInteger('payment_id');
            $table->timestamps();
            $table->foreign('pre_enrollment_id')->references('id')->on('pre_enrollments');
            $table->foreign('payment_id')->references('id')->on('payments');
        });


        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_groups');
    }
};
