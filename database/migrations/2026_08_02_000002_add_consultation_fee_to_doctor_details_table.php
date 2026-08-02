<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_details', function (Blueprint $table) {
            $table->decimal('consultation_fee', 10, 2)->default(0.00)->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('doctor_details', function (Blueprint $table) {
            $table->dropColumn('consultation_fee');
        });
    }
};
