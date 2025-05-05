<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->boolean('is_enabled')->default(true);
        });
        // $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        // DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
