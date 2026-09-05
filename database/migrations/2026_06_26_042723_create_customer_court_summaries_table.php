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
        Schema::create('customer_court_summary', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id');

            $table->integer('severity_total')->default(0);
            $table->integer('severity_high_relevance')->default(0);
            $table->integer('severity_high')->default(0);
            $table->integer('severity_medium')->default(0);
            $table->integer('severity_low')->default(0);


            $table->integer('civil_cases')->default(0);
            $table->integer('criminal_cases')->default(0);


            $table->integer('pending_cases')->default(0);
            $table->integer('disposed_cases')->default(0);
            $table->integer('not_available_cases')->default(0);
            $table->integer('total_cases')->default(0);


            $table->integer('district_courts')->default(0);
            $table->integer('high_courts')->default(0);
            $table->integer('consumer_courts')->default(0);
            $table->integer('supreme_courts')->default(0);
            $table->integer('tribunal_courts')->default(0);
            $table->integer('rera_courts')->default(0);
            $table->string('confidence_level')->nullable();


            $table->boolean('is_verified')->nullable();
            $table->json('api_response')->nullable();

            $table->integer('user_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_court_summary');
    }
};
