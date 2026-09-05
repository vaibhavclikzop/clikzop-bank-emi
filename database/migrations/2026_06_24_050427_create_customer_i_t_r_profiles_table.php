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
        Schema::create('customer_itr_profile', function (Blueprint $table) {
            $table->id();

            $table->text("customer_id")->unique();
            $table->enum("type", ["salaried", "business"])->default("salaried");
            $table->text("pan_no");
            $table->string("pan_no_last4");
            $table->string("name");
            $table->date("dob");
            $table->string("aadhaar");
            $table->string("passport_no")->nullable();
            $table->string("emplyrCat")->nullable();
            $table->string("emplyr_cat")->nullable();
            $table->string("status_of_entity")->nullable();
            $table->string("residential_status")->nullable();
            $table->string("pin_code")->nullable();
            $table->string("state_code")->nullable();
            $table->string("country_code")->nullable();
            $table->string("residence_no")->nullable();
            $table->string("road_or_street")->nullable();
            $table->string("residence_name")->nullable();
            $table->string("locality_of_area")->nullable();
            $table->string("city")->nullable();
            $table->string("std")->nullable();
            $table->string("email")->nullable();
            $table->string("phone_no")->nullable();
            $table->string("email2")->nullable();
            $table->string("mobile")->nullable();
            $table->string("mobile2")->nullable();
            $table->text("excelDownloadLink")->nullable();
            $table->text("pdfDownloadLink")->nullable();
            $table->string("user_id")->nullable();
            $table->json("api_response");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_itr_profile');
    }
};
