<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_code')->unique()->comment('Medical Record Number / No. RM');
            $table->string('nik')->nullable();
            $table->string('name');
            $table->string('gender')->nullable(); // L/P
            $table->date('birth_date')->nullable();
            $table->integer('age')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('department')->nullable();
            $table->string('employee_status')->nullable();
            $table->string('bpjs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};