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
        Schema::create('loan_itr_out_standing_demands', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("loan_id");
            $table->bigInteger("customer_id");
            $table->unsignedBigInteger('loan_itr_profile_id');
            $table->foreign('loan_itr_profile_id', 'loan_itr_osd')
                ->references('id')
                ->on('loan_itr_profile')
                ->onDelete('cascade');
            $table->string("rectification_rights")->nullable();
            $table->date("date_of_service")->nullable();
            $table->string("din")->nullable();
            $table->date("date_of_demandRaised")->nullable();
            $table->string("section_code")->nullable();
            $table->string("assessment_year")->nullable();
            $table->decimal("outstanding_demand_amount", 12, 2)->nullable();
            $table->string("mode_of_service")->nullable();
            $table->string("uploaded_by")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_itr_out_standing_demands');
    }
};
