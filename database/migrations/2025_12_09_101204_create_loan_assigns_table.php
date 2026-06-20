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
        Schema::create('loan_assigns', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode')->nullable();

            $table->unsignedBigInteger('loan_type_id');
            $table->unsignedBigInteger('interest_id');
            $table->unsignedBigInteger('collection_type_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('route_id');

            $table->integer('loan_amount');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_assigns');
    }
};
