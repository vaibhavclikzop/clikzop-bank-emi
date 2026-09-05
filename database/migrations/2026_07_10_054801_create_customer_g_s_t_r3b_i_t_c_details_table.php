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
        Schema::create('customer_gstr3b_itc_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gstr3b_details_id')
                ->constrained('customer_gstr3b_details')
                ->cascadeOnDelete();

            $table->string('section', 30);
            $table->string('type', 20);
            $table->decimal('iamt', 15, 2)->default(0);
            $table->decimal('camt', 15, 2)->default(0);
            $table->decimal('samt', 15, 2)->default(0);
            $table->decimal('csamt', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_gstr3b_itc_details');
    }
};
