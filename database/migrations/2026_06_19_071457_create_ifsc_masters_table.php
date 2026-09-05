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
        Schema::create('ifsc_masters', function (Blueprint $table) {
            $table->id();
            $table->integer('bank_id');
            $table->string('name')->nullable();
            $table->string('branch')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('micr')->nullable();
            $table->string('office')->nullable();
            $table->string('ifsc', 20)->nullable();
            $table->string('bank_code', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ifsc_masters');
    }
};
