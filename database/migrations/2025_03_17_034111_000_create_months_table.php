<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('months', function (Blueprint $table) {
            $table->id();
            $table->string('name', 15);
            $table->index('name');
            $table->unique('name');
        });

        DB::table('months')->insert([
            ['id' => 1, 'name' => 'ENERO'],
            ['id' => 2, 'name' => 'FEBRERO'],
            ['id' => 3, 'name' => 'MARZO'],
            ['id' => 4, 'name' => 'ABRIL'],
            ['id' => 5, 'name' => 'MAYO'],
            ['id' => 6, 'name' => 'JUNIO'],
            ['id' => 7, 'name' => 'JULIO'],
            ['id' => 8, 'name' => 'AGOSTO'],
            ['id' => 9, 'name' => 'SEPTIEMBRE'],
            ['id' => 10, 'name' => 'OCTUBRE'],
            ['id' => 11, 'name' => 'NOVIEMBRE'],
            ['id' => 12, 'name' => 'DICIEMBRE'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('months');
    }
};
