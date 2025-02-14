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
        //eliminar tabla pa
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->char('sequence_number', 15);
            $table->unsignedBigInteger('payment_type_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('amount_used', 10, 2)->default(0);
            $table->date('date')->nullable();
            $table->boolean('is_enabled')->default(1);
            $table->timestamps();
            // $table->unique(['sequence_number', 'date', 'student_id']);
            $table->foreign('student_id')->references('id')->on('students')->onDelete('no action');
            $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('no action');
        });

        DB::statement('ALTER TABLE payments ADD CONSTRAINT check_amount_used CHECK (amount_used <= amount)');

        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payments DROP CONSTRAINT check_amount_used");
        Schema::dropIfExists('payments');
    }
};
