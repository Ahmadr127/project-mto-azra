<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcu_exam_medical_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mcu_registration_id')->constrained('mcu_registrations')->onDelete('cascade');
            $table->foreignId('mcu_medical_action_id')->constrained('mcu_medical_actions')->onDelete('cascade');
            $table->text('result')->nullable();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcu_exam_medical_actions');
    }
};