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
        Schema::create('loan_cibil_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("loan_cibil_mst_id");
            $table->foreign('loan_cibil_mst_id')
                ->references('id')
                ->on('loan_cibil_data_mst')
                ->onDelete('cascade');

            $table->string("member_name")->nullable();
            $table->string("account_type")->nullable();
            $table->string("account_number")->nullable();
            $table->string("ownership_indicator")->nullable();
            $table->string("account_status")->nullable();
            $table->string("credit_limit")->nullable();
            $table->string("sanctioned_amount")->nullable();
            $table->string("current_balance")->nullable();
            $table->string("cash_limit")->nullable();
            $table->string("amount_overdue")->nullable();
            $table->string("rate_of_interest")->nullable();
            $table->string("repayment_tenure")->nullable();
            $table->string("emi_amount")->nullable();
            $table->string("payment_frequency")->nullable();
            $table->string("date_opened")->nullable();
            $table->string("date_closed")->nullable();
            $table->string("date_reported")->nullable();
            $table->json("payment_history")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_cibil_accounts');
    }
};
