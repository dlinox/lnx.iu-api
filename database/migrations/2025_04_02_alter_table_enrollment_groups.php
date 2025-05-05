<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::table('enrollment_groups', function (Blueprint $table) {
        //     $table->unsignedBigInteger('created_by')->nullable()->after('status'); // ID del usuario que creó la matrícula
        //     $table->enum('enrollment_modality', ['PRESENCIAL', 'VIRTUAL'])->default('PRESENCIAL')->after('status'); // Modalidad del grupo
        // });
    }
    public function down(): void
    {
        // Schema::table('enrollment_groups', function (Blueprint $table) {
        //     $table->dropColumn('created_by');
        //     $table->dropColumn('enrollment_modality');
        // });
    }
};
