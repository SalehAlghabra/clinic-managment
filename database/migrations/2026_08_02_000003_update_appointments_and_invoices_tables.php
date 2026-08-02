<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->decimal('consultation_fee', 10, 2)->default(0.00)->after('doctor_id');
            $table->decimal('additional_cost', 10, 2)->default(0.00)->after('notes');
            $table->text('additional_note')->nullable()->after('additional_cost');
            
            if (Schema::hasColumn('appointments', 'service_id')) {
                $table->unsignedBigInteger('service_id')->nullable()->change();
            }
        });

        // Modify payment_method column in invoices table to allow 'wallet'
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('payment_method', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['consultation_fee', 'additional_cost', 'additional_note']);
        });
    }
};
