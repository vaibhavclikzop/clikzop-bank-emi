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
        Schema::create('customer_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('document_id')
                ->constrained('common_documents');

            $table->string('document_name');

            $table->text('document_no')->nullable();
            $table->string('document_no_last4', 4)->nullable();

            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('pincode')->nullable();

            $table->string('file')->nullable();

            $table->boolean('is_verified')->default(false);

            $table->timestamp('verified_at')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_documents');
    }
};
