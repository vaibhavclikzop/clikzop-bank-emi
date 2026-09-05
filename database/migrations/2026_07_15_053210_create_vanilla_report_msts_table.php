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
        Schema::create('loan_vanilla_report_mst', function (Blueprint $table) {
            $table->id();
            $table->integer("loan_id");
            $table->integer("customer_id");
            $table->decimal("total", 15, 2)->default(0);
            $table->integer("user_id");
            $table->enum("applicant_type", ["applicant", "co-applicant"])->default("applicant");
            $table->timestamps();
            $table->unique(['loan_id', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_vanilla_report_mst');
    }
};
