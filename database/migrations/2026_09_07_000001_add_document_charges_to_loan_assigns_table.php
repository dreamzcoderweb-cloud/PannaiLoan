<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_assigns', function (Blueprint $table) {
            $table->decimal('document_charges', 12, 2)->nullable()->default(0)->after('monthly_emi');
        });
    }

    public function down(): void
    {
        Schema::table('loan_assigns', function (Blueprint $table) {
            $table->dropColumn('document_charges');
        });
    }
};
