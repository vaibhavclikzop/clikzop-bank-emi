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
        Schema::create('loan_gstr1_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gst_details_id')
                ->constrained('loan_gst_details')
                ->cascadeOnDelete();
            $table->string('ret_period')->nullable();
            $table->string('section_name')->nullable();
            $table->string('checksum')->nullable();
            $table->integer('total_records')->default(0);
            $table->decimal('taxable_value', 15, 2)->default(0);
            $table->decimal('igst', 15, 2)->default(0);
            $table->decimal('cgst', 15, 2)->default(0);
            $table->decimal('sgst', 15, 2)->default(0);
            $table->decimal('cess', 15, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_gstr1_details');
    }
};
