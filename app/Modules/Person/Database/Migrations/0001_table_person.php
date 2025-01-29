<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->char('code', 8)->unique();
            $table->unsignedBigInteger('document_type_id')->nullable();
            $table->char('document_number', 15)->nullable();
            $table->string('name', 80);
            $table->string('last_name_father', 80)->nullable();
            $table->string('last_name_mother', 80)->nullable();
            $table->integer('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('address', 150)->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('email', 80)->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();

            $table->index('code', 'IDX_PERSON_CODE');
            $table->index('document_number', 'IDX_PERSON_DOCUMENT_NUMBER');
            $table->index(['name', 'last_name_father', 'last_name_mother'], 'IDX_PERSON_FULL_NAME');

            $table->foreign('document_type_id')->references('id')->on('document_types');
            // $table->foreign('location_id')->references('id')->on('locations');
            // $table->foreign('country_id')->references('id')->on('countries');
            $table->timestamps();
        });

        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
