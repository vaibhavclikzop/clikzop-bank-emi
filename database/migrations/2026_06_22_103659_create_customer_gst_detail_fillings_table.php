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
        Schema::create('customer_gst_detail_fillings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('gst_details_id');
            $table->string('valid')->nullable();
            $table->string('mof')->nullable();
            $table->date('dof')->nullable();
            $table->string('return_type')->nullable();
            $table->string('ret_prd')->nullable();
            $table->string('arn')->nullable();
            $table->string('status')->nullable();
            $table->date('due_date')->nullable();
            $table->string('is_delay')->nullable();
            $table->string('delay_days')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_gst_detail_fillings');
    }
};
