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
        Schema::create('loan_gstr3b_supply_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gstr3b_details_id')
                ->constrained('loan_gstr3b_details')
                ->cascadeOnDelete();
            $table->string('type', 50);
            $table->decimal('txval', 15, 2)->default(0);
            $table->decimal('iamt', 15, 2)->default(0);
            $table->decimal('camt', 15, 2)->default(0);
            $table->decimal('samt', 15, 2)->default(0);
            $table->decimal('csamt', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_gstr3b_supply_details');
    }
};
