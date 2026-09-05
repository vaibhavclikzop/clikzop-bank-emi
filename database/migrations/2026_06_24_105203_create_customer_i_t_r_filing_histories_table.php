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
        Schema::create('customer_itr_filing_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("customer_id");
            $table->unsignedBigInteger('customer_itr_profile_id');

            $table->foreign('customer_itr_profile_id')
                ->references('id')
                ->on('customer_itr_profile')
                ->onDelete('cascade');
            $table->string("pan_no")->nullable();
            $table->string("pan_no_last4")->nullable();
            $table->string("assessment_year")->nullable();
            $table->string("form")->nullable();
            $table->string("filingType")->nullable();
            $table->string("status")->nullable();
            $table->date("date")->nullable();
            $table->text("downloadLink")->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_i_t_r_filing_histories');
    }
};
