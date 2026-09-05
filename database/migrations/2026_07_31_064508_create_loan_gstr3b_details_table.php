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
        Schema::create('loan_gstr3b_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gst_details_id')
                ->constrained('loan_gst_details')
                ->cascadeOnDelete();
            $table->date('ret_period');
            $table->decimal('ttl_tax_payable', 15, 2)->default(0);
            $table->decimal('ttl_tax_paid', 15, 2)->default(0);
            $table->decimal('ttl_itc_paid', 15, 2)->default(0);
            $table->decimal('ttl_cash_paid', 15, 2)->default(0);
            $table->decimal('ttl_late_fee', 15, 2)->default(0);
            $table->decimal('ttl_interest', 15, 2)->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->default(0);
            $table->decimal('itc_avl_by_gstr2a', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_gstr3b_details');
    }
};
