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
        Schema::create('emicollections', function (Blueprint $table) {
            $table->id();
            $table->integer('loan_assign_id');
            $table->integer('client_id');
            $table->integer('collection_type_id');
            $table->string('total_payable_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emicollections');
    }
};
