<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (! Schema::hasColumn('patients', 'birth_place')) {
                $table->string('birth_place')->nullable()->after('birth_date');
            }
            if (! Schema::hasColumn('patients', 'emergency_phone')) {
                $table->string('emergency_phone')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('patients', 'photo')) {
                $table->string('photo')->nullable()->after('emergency_phone');
            }
            if (! Schema::hasColumn('patients', 'kelurahan')) {
                $table->string('kelurahan')->nullable()->after('address');
            }
            if (! Schema::hasColumn('patients', 'kecamatan')) {
                $table->string('kecamatan')->nullable()->after('kelurahan');
            }
            if (! Schema::hasColumn('patients', 'kabupaten_kota')) {
                $table->string('kabupaten_kota')->nullable()->after('kecamatan');
            }
            if (! Schema::hasColumn('patients', 'provinsi')) {
                $table->string('provinsi')->nullable()->after('kabupaten_kota');
            }
            if (! Schema::hasColumn('patients', 'kode_pos')) {
                $table->string('kode_pos', 10)->nullable()->after('provinsi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $columns = ['birth_place', 'emergency_phone', 'photo', 'kelurahan', 'kecamatan', 'kabupaten_kota', 'provinsi', 'kode_pos'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('patients', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
