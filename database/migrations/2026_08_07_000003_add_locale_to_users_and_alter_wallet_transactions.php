<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add locale column to users table if not exists
        if (!Schema::hasColumn('users', 'locale')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('locale', 10)->default('en')->after('role');
            });
        }

        // 2. Modify type column in wallet_transactions table to string(50)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `wallet_transactions` MODIFY COLUMN `type` VARCHAR(50) NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'locale')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('locale');
            });
        }
    }
};
