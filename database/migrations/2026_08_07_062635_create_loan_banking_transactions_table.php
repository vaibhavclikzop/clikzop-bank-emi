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
        Schema::create('loan_banking_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("loan_id");
            $table->unsignedBigInteger("loan_banking_mst_id");
            $table->string("txn_date")->nullable();
            $table->string("value_date")->nullable();
            $table->string("description")->nullable();
            $table->string("cheque_number")->nullable();
            $table->string("transaction_id")->nullable();
            $table->string("reference_number")->nullable();
            $table->string("upi_id")->nullable();
            $table->string("mode")->nullable();
            $table->decimal("debit", 15, 2)->nullable();
            $table->decimal("credit", 15, 2)->nullable();
            $table->decimal("balance", 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_banking_transactions');
    }
};
