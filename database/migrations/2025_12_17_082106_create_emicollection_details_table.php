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
        Schema::create('emicollection_details', function (Blueprint $table) {
            $table->id();
            $table->Integer('emi_collection_id');
            $table->date('due_date');
            $table->decimal('emi_amount', 10, 2);
            $table->decimal('remaining_payable_amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emicollection_details');
    }
};
