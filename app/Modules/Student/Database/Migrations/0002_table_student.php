<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            
            $table->id();
            $table->string('ref')->nullable();
            $table->unsignedBigInteger('student_type_id')->nullable();
            $table->char('code', 8)->unique();
            $table->unsignedBigInteger('document_type_id')->nullable();
            $table->string('document_number', 20)->nullable();
            $table->string('name', 50)->nullable();
            $table->string('last_name_father', 50)->nullable();
            $table->string('last_name_mother', 50)->nullable();
            $table->unsignedBigInteger('gender_id')->nullable();
            $table->string('phone', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('address', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            //references
            $table->foreign('student_type_id')->references('id')->on('student_types');
            $table->foreign('document_type_id')->references('id')->on('document_types');
            $table->foreign('gender_id')->references('id')->on('genders');
            // $table->foreign('location_id')->references('id')->on('locations');
            // $table->foreign('country_id')->references('id')->on('countries');
            //indexes
            $table->index('student_type_id');
            $table->index('code');
            $table->index('document_number');
            $table->index('name');
            $table->index('email');
            $table->index('phone');
            $table->index('last_name_father');
            $table->index('last_name_mother');
            //fulltext indexes name
            $table->index(['name', 'last_name_father', 'last_name_mother']);
        });

        // $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        // DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
