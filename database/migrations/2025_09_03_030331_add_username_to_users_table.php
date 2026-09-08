<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
        });

        // Update existing users with a default username based on their id (DB-agnostic)
        $users = DB::table('users')->whereNull('username')->get();
        foreach ($users as $user) {
            DB::table('users')->where('id', $user->id)->update(['username' => 'user_'.$user->id]);
        }

        // Make username not null and unique (skip change on sqlite which doesn't support ->change without doctrine/dbal)
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable(false)->change();
            });
        }
        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
