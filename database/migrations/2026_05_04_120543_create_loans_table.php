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

        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();

            $table->string('loan_number')->unique();
            $table->integer('loan_type_id');
            $table->integer('income_type_id');

            $table->decimal('loan_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->integer('tenure_months')->nullable();
            $table->string('loan_purpose')->nullable();

            $table->decimal('emi_amount', 12, 2)->nullable();
            $table->decimal('total_interest', 12, 2)->nullable();
            $table->decimal('total_payable', 12, 2)->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->unsignedBigInteger('status_id')->index();

            $table->string('bank_name')->nullable();
            $table->text('remarks')->nullable();

            $table->boolean('kyc_verified')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
