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
        Schema::create('loan_vanilla_report_values', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("loan_vanilla_report_field_id");
            $table->bigInteger("vanilla_field_id");
            $table->bigInteger("vanilla_report_year_id");
            $table->string("field_name");
            $table->string("display_name");
            $table->decimal("amount", 15, 2);
            $table->decimal("eligibility_percentage", 15, 2);
            $table->decimal("eligible_amount", 15, 2);
            $table->decimal("display_order", 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_vanilla_report_values');
    }
};
