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
       Schema::table('interests', function (Blueprint $table) {
    $table->integer('collection_type')->nullable()->comment('1=Daily, 2=Weekly, 3=Monthly');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interests', function (Blueprint $table) {
            $table->dropColumn('collection_type');
        });
    }
};
