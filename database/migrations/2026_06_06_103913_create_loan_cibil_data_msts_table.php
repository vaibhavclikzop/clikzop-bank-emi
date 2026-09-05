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
        Schema::create('loan_cibil_data_mst', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedBigInteger('bank_id');
            $table->string('loan_account_no');
            $table->decimal('loan_amount', 15, 2)->default(0);
            $table->decimal('outstanding_amount', 15, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->unsignedBigInteger('loan_type_id');
            $table->integer('tenure')->default(0);
            $table->integer('tenure_left')->default(0);
            $table->unsignedBigInteger('cibil_status_id');
            $table->decimal('overdue', 15, 2)->default(0);
            $table->string('emi_bank_name')->nullable();
            $table->unsignedBigInteger('account_type_id')->nullable();
            $table->string('paid_account_no')->nullable();
            $table->decimal('emi_amount', 15, 2)->default(0);
            $table->decimal('obligate_amount', 15, 2)->default(0);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_cibil_data_mst');
    }
};
