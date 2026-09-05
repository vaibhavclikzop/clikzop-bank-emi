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
        Schema::create('loan_banking_consents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("loan_id");
            $table->string("consent_id");
            $table->string("url");
            $table->date("from_date");
            $table->date("to_date");
            $table->string("status");
            $table->string("fl_status");
            $table->string("fl_id");
            $table->timestamp("fl_date_time");
            $table->json("response");
            $table->integer("user_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_banking_consents');
    }
};
