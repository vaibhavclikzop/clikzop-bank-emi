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
        Schema::create('customer_banks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->text('account_no');
            $table->text('account_holder_name');
            $table->string('account_no_last4', 4);
            $table->integer('bank_id');
            $table->integer('ifsc_master_id');
            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('micr')->nullable();
            $table->string('office')->nullable();
            $table->string('ifsc', 20)->nullable();
            $table->string('bank_transaction_status')->nullable();
            $table->string('bank_rrn')->nullable();
            $table->string('bank_response')->nullable();
            $table->string('status_code')->nullable();
            $table->string('status_as_per_source')->nullable();
            $table->string('is_valid')->nullable();
            $table->string('karza_request_id')->nullable();
            $table->json('api_response')->nullable();
            $table->string('account_no_hash')->index();
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_banks');
    }
};
