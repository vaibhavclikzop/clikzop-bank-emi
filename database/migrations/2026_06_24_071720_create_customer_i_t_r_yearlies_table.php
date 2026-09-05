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
        Schema::create('customer_itr_yearly', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("customer_id");
            $table->unsignedBigInteger('customer_itr_profile_id');

            $table->foreign('customer_itr_profile_id')
                ->references('id')
                ->on('customer_itr_profile')
                ->onDelete('cascade');
            $table->string("form_name")->nullable();
            $table->string("form_version")->nullable();
            $table->string("description")->nullable();
            $table->string("financial_year")->nullable();
            $table->string("schema_version")->nullable();
            $table->string("assessment_year")->nullable();
            $table->date("filing_date");
            $table->decimal("immovable_assets", 12, 2)->nullable();
            $table->decimal("movable_assets", 12, 2)->nullable();
            $table->decimal("financial_assets", 12, 2)->nullable();
            $table->decimal("total_liabilities", 12, 2)->nullable();
            $table->decimal("refund", 12, 2)->nullable();
            $table->decimal("taxes_paid", 12, 2)->nullable();
            $table->decimal("aggregate_liability", 12, 2)->nullable();
            $table->decimal("net_tax_liability", 12, 2)->nullable();
            $table->decimal("total_interest_and_fee_payable", 12, 2)->nullable();
            $table->decimal("total_advance_tax_paid", 12, 2)->nullable();
            $table->decimal("total_tds_claimed", 12, 2)->nullable();
            $table->decimal("total_tcs_claimed", 12, 2)->nullable();
            $table->decimal("total_self_assessment_tax_paid", 12, 2)->nullable();
            $table->decimal("amount_payable", 12, 2)->nullable();
            $table->decimal("salary", 12, 2)->nullable();
            $table->decimal("house_property", 12, 2)->nullable();
            $table->decimal("other_sources", 12, 2)->nullable();
            $table->decimal("capital_gains", 12, 2)->nullable();
            $table->string("bank_name")->nullable();
            $table->string("account_no")->nullable();
            $table->string("account_no_last4")->nullable();
            $table->string("ifsc_code")->nullable();
            $table->boolean("use_for_refund")->nullable();
            $table->boolean("delay")->nullable();
            $table->boolean("default")->nullable();
            $table->boolean("revised")->nullable();
            $table->boolean("late_fee")->nullable();
            $table->boolean("interest_fee")->nullable();
            $table->boolean("demand_notice")->nullable();
            $table->boolean("high_val_transaction")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_i_t_r_yearlies');
    }
};
