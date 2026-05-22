<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mcu_labs', function (Blueprint $table) {
            $table->string('normal_value')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('mcu_labs', function (Blueprint $table) {
            $table->dropColumn('normal_value');
        });
    }
};