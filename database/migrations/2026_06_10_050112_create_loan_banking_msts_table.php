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
        Schema::create('loan_banking_mst', function (Blueprint $table) {
            $table->id();
            $table->string('loan_id')->nullable();
            $table->string('account_holder')->nullable();
            $table->integer('bank_id')->nullable();
            $table->string('account_number')->nullable();
            $table->integer('account_type_id')->nullable();
            $table->decimal('total_6_month_balance', 15, 2)->default(0);
            $table->decimal('total_12_month_balance', 15, 2)->default(0);
            $table->decimal('avg_6_month_balance', 15, 2)->default(0);
            $table->decimal('avg_12_month_balance', 15, 2)->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_banking_mst');
    }
};
