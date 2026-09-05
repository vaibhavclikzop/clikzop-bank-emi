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
        Schema::create('loan_vanilla_report_fields', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("vanilla_report_mst_id");
            $table->bigInteger("vanilla_field_id");
            $table->decimal("average", 15, 2)->default(0);
            $table->decimal("eligible_percentage", 15, 2)->default(0);
            $table->decimal("eligible_income", 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_vanilla_report_fields');
    }
};
