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
        Schema::create('loan_banking_det', function (Blueprint $table) {
            $table->id();
            $table->integer('loan_id');
            $table->integer('mst_id');
            $table->integer('month');
            $table->string('month_name');

            $table->decimal('closing_balance_5', 15, 2)->default(0);
            $table->decimal('closing_balance_15', 15, 2)->default(0);
            $table->decimal('closing_balance_20', 15, 2)->default(0);
            $table->decimal('closing_balance_25', 15, 2)->default(0);
            $table->decimal('closing_balance_30', 15, 2)->default(0);

            $table->decimal('monthly_total', 15, 2)->default(0);
            $table->decimal('monthly_average', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_banking_det');
    }
};
