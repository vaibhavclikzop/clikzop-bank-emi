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
        Schema::create('loan_cibil_identifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("loan_cibil_mst_id");
            $table->foreign('loan_cibil_mst_id')
                ->references('id')
                ->on('loan_cibil_data_mst')
                ->onDelete('cascade');
                $table->string("type")->nullable();
                $table->string("number")->nullable();
                $table->string("issue_date")->nullable();
                $table->string("expiry_date")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_cibil_identifications');
    }
};
