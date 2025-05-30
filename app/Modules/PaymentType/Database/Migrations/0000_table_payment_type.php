<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->decimal('commission', 5, 2)->default(1.00);
            $table->boolean('is_enabled')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_types');
    }
};
