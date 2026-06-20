<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_summary', function (Blueprint $table) {
            $table->bigIncrements('collection_sum_id');

            $table->date('previous_date')->nullable();
            $table->decimal('previous_total_paidamount', 15, 2)->default(0);

            $table->date('current_date');
            $table->decimal('current_total_paidamount', 15, 2)->default(0);

            $table->decimal('total_amount', 15, 2)->default(0);
            $table->unsignedInteger('total_loan')->default(0);
            $table->decimal('total_loanamount', 15, 2)->default(0);
            $table->decimal('expense_amount_currentdate', 15, 2)->default(0);
            $table->decimal('final_balance_amount', 15, 2)->default(0);

            $table->timestamps();

            $table->unique('current_date', 'collection_summary_current_date_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_summary');
    }
};

