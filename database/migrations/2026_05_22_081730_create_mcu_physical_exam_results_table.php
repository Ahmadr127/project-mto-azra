<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcu_physical_exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mcu_registration_id')->constrained('mcu_registrations')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('vital_signs')->nullable();
            $table->json('head_and_neck')->nullable();
            $table->json('thorax')->nullable();
            $table->json('abdomen')->nullable();
            $table->json('urogenital')->nullable();
            $table->json('extremities')->nullable();
            $table->json('others')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcu_physical_exam_results');
    }
};