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
        Schema::create('loan_gst_running_year_det', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('loan_id');
            $table->bigInteger('mst_id');
            $table->decimal('amount', 15, 2)->default(0);
            $table->integer('month');
            $table->string('month_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_g_s_t_running_year_dets');
    }
};
