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
        Schema::create('loan_vanilla_report_years', function (Blueprint $table) {
            $table->id();
            $table->integer("vanilla_mst_id");
            $table->string('financial_year', 9);
            $table->decimal("turnover", 15, 2);
            $table->decimal("gross_profit", 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_vanilla_report_years');
    }
};
