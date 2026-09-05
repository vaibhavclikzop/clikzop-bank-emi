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
        Schema::create('loan_vanilla_report_summary', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("loan_id");
            $table->decimal("total_annual_income", 15, 2)->default(0);
            $table->decimal("appraised_monthly_income", 15, 2)->default(0);
            $table->decimal("appraised_obligations", 15, 2)->default(0);
            $table->decimal("net_appraised_income", 15, 2)->default(0);
            $table->decimal("foir", 15, 2)->default(0);
            $table->decimal("ltv", 15, 2)->default(0);
            $table->decimal("ltv_foir", 15, 2)->default(0);
            $table->decimal("max_emi", 15, 2)->default(0);
            $table->decimal("tenor", 15, 2)->default(0);
            $table->decimal("interest_rate", 15, 2)->default(0);
            $table->decimal("emi_factor", 15, 2)->default(0);
            $table->decimal("eligibility", 15, 2)->default(0);
            $table->enum("double", ["Yes", "No"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_vanilla_report_summary');
    }
};
