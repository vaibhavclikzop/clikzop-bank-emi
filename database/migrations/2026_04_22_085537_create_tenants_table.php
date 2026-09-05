<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // DSA name
            $table->string('company_name')->nullable();
            $table->string('email')->unique();
            $table->string('mobile')->nullable();
            $table->boolean('status')->default(1);
            $table->string('domain')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('pan_last4')->nullable()->index();
            $table->string('aadhar_no')->nullable();
            $table->string('aadhar_last4')->nullable()->index();
            $table->string('gst_in')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('pincode')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('bank_account_no_last4')->nullable()->index();
            $table->string('ifsc_code')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
