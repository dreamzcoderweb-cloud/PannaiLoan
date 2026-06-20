<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collection_summary', function (Blueprint $table) {
            $table->decimal('md_fund_in', 15, 2)->default(0)->after('current_total_paidamount');
            $table->decimal('md_fund_out', 15, 2)->default(0)->after('expense_amount_currentdate');
        });
    }

    public function down(): void
    {
        Schema::table('collection_summary', function (Blueprint $table) {
            $table->dropColumn(['md_fund_in', 'md_fund_out']);
        });
    }
};
