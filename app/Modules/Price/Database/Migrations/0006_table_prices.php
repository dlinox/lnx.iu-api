<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_id');
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('student_type_id');
            $table->decimal('enrollment_price', 10, 2);
            $table->decimal('presential_price', 10, 2)->nullable();
            $table->decimal('virtual_price', 10, 2)->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
