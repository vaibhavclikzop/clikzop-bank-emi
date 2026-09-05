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
        Schema::create('loan_gst_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->bigInteger('loan_id');
            $table->bigInteger('company_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('email_id')->nullable();
            $table->string('gst_in');
            $table->string('gst_in_ref')->nullable();
            $table->string('mobile')->nullable();
            $table->string('state')->nullable();
            $table->string('pan')->nullable();
            $table->string('registration_name')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('stjCd')->nullable();
            $table->string('dty')->nullable();
            $table->string('stj')->nullable();
            $table->string('nba')->nullable();
            $table->string('ctb')->nullable();
            $table->date('registration_date')->nullable();
            $table->string('address')->nullable();
            $table->string('trade_name')->nullable();
            $table->string('ctjCd')->nullable();
            $table->string('status')->nullable();
            $table->string('ctj')->nullable();
            $table->string('e_invoice_status')->nullable();
            $table->string('user_id')->nullable();
            $table->json('api_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_gst_details');
    }
};
