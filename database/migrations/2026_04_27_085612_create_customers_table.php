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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('number')->unique();

            $table->string('email')->nullable();
            $table->string('password')->nullable();

            $table->date('dob')->nullable();
            $table->date('doa')->nullable();

            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('pincode')->nullable();

            $table->enum('signup_document_type', [
                'aadhaar',
                'pan',
                'driving_license',
                'voter_id',
                'passport',
            ]);

            $table->text('signup_document')->nullable(); // encrypted
            $table->string('signup_document_last4')->nullable()->index();

            $table->text('pan_no')->nullable();
            $table->string('pan_last4')->nullable()->index();

            $table->text('aadhar_no')->nullable();
            $table->string('aadhar_last4')->nullable()->index();

            $table->text('driving_license_no')->nullable();
            $table->string('dl_last4')->nullable()->index();

            $table->text('passport_no')->nullable();
            $table->string('passport_no_last4')->nullable()->index();

            $table->text('voter_id_no')->nullable();
            $table->string('voter_id_no_last4')->nullable()->index();

            $table->text('gst_no')->nullable();
            $table->integer("pan_verified")->default(0);
            $table->integer("adhar_verified")->default(0);
            $table->bigInteger('tenant_id')->nullable();
            $table->bigInteger('user_id')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
