<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('deposit_amount', 10, 2)->default(0)->after('total_amount');
            $table->decimal('remaining_amount', 10, 2)->default(0)->after('deposit_amount');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['deposit_amount', 'remaining_amount']);
        });
    }
};
