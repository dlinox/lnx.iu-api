<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('payments');
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->char('sequence_number', 15);
            $table->unsignedBigInteger('payment_type_id');
            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->string('ref')->nullable();
            $table->boolean('is_used')->default(0);
            $table->boolean('is_enabled')->default(1);
            $table->timestamps();

            $table->index('student_id');
            $table->unique(['sequence_number', 'date', 'amount']);
            $table->foreign('student_id')->references('id')->on('students')->onDelete('restrict');
            $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('restrict');
        });

        // DB::statement('ALTER TABLE payments ADD CONSTRAINT check_amount_used CHECK (amount_used <= amount)');
        // $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        // DB::unprepared($sql);
    }

    public function down(): void
    {
        // DB::statement("ALTER TABLE payments DROP CONSTRAINT check_amount_used");
        Schema::dropIfExists('payments');
    }
};
