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
        Schema::create('loan_gst_running_year_mst', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('loan_id');
            $table->string('company')->nullable();
            $table->string('location')->nullable();
            $table->year('year')->nullable();
            $table->string('financial_year')->nullable();
            $table->decimal('average_turnover', 15, 2)->default(0);
            $table->decimal('total_turnover', 15, 2)->default(0);
            $table->bigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_g_s_t_running_year_msts');
    }
};
