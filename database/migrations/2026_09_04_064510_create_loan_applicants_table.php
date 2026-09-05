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
        Schema::create('loan_applicants', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("loan_id");
            $table->bigInteger("customer_id");
            $table->enum("applicant_type", ["applicant", "co_applicant"]);
            $table->enum("financial_status", ["financial", "non_financial"]);
            $table->integer("relationship_id");
            $table->integer("is_primary");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_applicants');
    }
};
