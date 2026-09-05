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
        Schema::create('loan_lip_report_det', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("loan_lip_report_mst_id");
            $table->bigInteger("loan_id");
            $table->bigInteger("customer_id");
            $table->enum("applicant_type", ["applicant", "con-applicant"]);
            $table->decimal("loan_amount", 15, 2)->default(0);
            $table->decimal("profit_before_tax_current", 15, 2)->default(0);
            $table->decimal("profit_before_tax_previous", 15, 2)->default(0);
            $table->decimal("total_profits", 15, 2)->default(0);
            $table->decimal("lip_income", 15, 2)->default(0);
            $table->decimal("depreciation_current", 15, 2)->default(0);
            $table->decimal("depreciation_previous", 15, 2)->default(0);
            $table->decimal("avg_depreciation", 15, 2)->default(0);
            $table->decimal("total_cash_profits", 15, 2)->default(0);
            $table->decimal("other_income", 15, 2)->default(0);
            $table->decimal("total_monthly_income", 15, 2)->default(0);
            $table->decimal("gross_eligible_income", 15, 2)->default(0);
            $table->decimal("foir", 15, 2)->default(0);
            $table->decimal("interest_rate", 15, 2)->default(0);
            $table->decimal("tenor", 15, 2)->default(0);
            $table->decimal("emi_per_lakh", 15, 2)->default(0);
            $table->decimal("max_eligible_loan", 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_lip_report_det');
    }
};
