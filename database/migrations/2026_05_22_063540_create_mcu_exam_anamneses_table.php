<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcu_exam_anamneses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mcu_registration_id')->constrained('mcu_registrations')->onDelete('cascade');
            $table->foreignId('mcu_anamnesis_id')->constrained('mcu_anamneses')->onDelete('cascade');
            $table->text('result')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcu_exam_anamneses');
    }
};