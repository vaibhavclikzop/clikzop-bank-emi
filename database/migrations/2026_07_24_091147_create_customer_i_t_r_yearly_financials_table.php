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
        Schema::create('customer_itr_yearly_financial', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("customer_id");
            $table->bigInteger("customer_itr_yearly_id");
            $table->decimal("total_current_liability", 15, 2)->default(0);
            $table->decimal("total_inventory", 15, 2)->default(0);
            $table->decimal("total_assets", 15, 2)->default(0);
            $table->decimal("trade_receivables", 15, 2)->default(0);
            $table->decimal("total_liability", 15, 2)->default(0);
            $table->decimal("trade_payable", 15, 2)->default(0);
            $table->decimal("total_investment", 15, 2)->default(0);
            $table->decimal("total_el", 15, 2)->default(0);
            $table->decimal("total_fixed_asset", 15, 2)->default(0);
            $table->decimal("total_equity", 15, 2)->default(0);
            $table->decimal("cash_and_cash_eqv", 15, 2)->default(0);
            $table->decimal("total_current_asset", 15, 2)->default(0);
            $table->decimal("non_operating_turnover", 15, 2)->default(0);
            $table->decimal("operating_turnover", 15, 2)->default(0);
            $table->decimal("profit_before_tax", 15, 2)->default(0);
            $table->decimal("direct_cost", 15, 2)->default(0);
            $table->decimal("purchases", 15, 2)->default(0);
            $table->decimal("interest_expense", 15, 2)->default(0);
            $table->decimal("total_expense", 15, 2)->default(0);
            $table->decimal("income_from_bp", 15, 2)->default(0);
            $table->decimal("income_from_os", 15, 2)->default(0);
            $table->decimal("profit_after_tax", 15, 2)->default(0);
            $table->decimal("gross_profit", 15, 2)->default(0);
            $table->decimal("income_from_salary", 15, 2)->default(0);
            $table->decimal("income_from_hp", 15, 2)->default(0);
            $table->decimal("total_tax", 15, 2)->default(0);
            $table->decimal("income_from_cg", 15, 2)->default(0);
            $table->decimal("ebitda", 15, 2)->default(0);
            $table->decimal("total_revenue", 15, 2)->default(0);
            $table->decimal("revenue_from_operations", 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_itr_yearly_financial');
    }
};
