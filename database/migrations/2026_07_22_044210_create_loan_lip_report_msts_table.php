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
        Schema::create('loan_lip_report_mst', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("loan_id")->unique();
            $table->bigInteger("total_eligible_loan")->default(0);
            $table->bigInteger("net_eligible_loan")->default(0);
            $table->text("remarks")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_lip_report_mst');
    }
};
