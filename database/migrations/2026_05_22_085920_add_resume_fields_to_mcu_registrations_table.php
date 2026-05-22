<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mcu_registrations', function (Blueprint $table) {
            $table->text('conclusion')->nullable();
            $table->text('recommendation')->nullable();
            $table->foreignId('resume_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('resume_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mcu_registrations', function (Blueprint $table) {
            $table->dropForeign(['resume_by']);
            $table->dropColumn(['conclusion', 'recommendation', 'resume_by', 'resume_date']);
        });
    }
};
