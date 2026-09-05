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
        Schema::create('cibil_scores', function (Blueprint $table) {
            $table->id();
            $table->string("pan_no")->nullable();
            $table->string("mobile_number")->nullable();
            $table->string("full_name")->nullable();
            $table->string("current_credit")->nullable();
            $table->string("credit_used")->nullable();
            $table->text("report_url")->nullable();
            $table->text("web_url")->nullable();
            $table->text("response_key")->nullable();
            $table->string("Oldest_credit_account_period")->nullable();
            $table->string("inquires")->nullable();
            $table->string("on_time_payment_history")->nullable();
            $table->string("credit_card_utilization")->nullable();
            $table->string("credit_mix")->nullable();
            $table->string("date")->nullable();
            $table->string("risk_score")->nullable();
            $table->string("population_rank")->nullable();
            $table->string("fore_name")->nullable();
            $table->bigInteger("user_id")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cibil_scores');
    }
};
