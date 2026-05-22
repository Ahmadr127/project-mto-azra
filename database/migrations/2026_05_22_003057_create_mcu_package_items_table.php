<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mcu_package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mcu_package_id')->constrained('mcu_packages')->onDelete('cascade');
            $table->morphs('item');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mcu_package_items');
    }
};
