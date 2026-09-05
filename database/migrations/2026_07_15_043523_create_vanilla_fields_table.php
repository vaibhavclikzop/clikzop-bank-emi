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
        Schema::create('vanilla_fields', function (Blueprint $table) {
            $table->id();
            $table->string("field_name")->unique();
            $table->string("display_name")->nullable();
            $table->integer("display_order")->nullable();
            $table->enum("status",["active","inactive"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vanilla_fields');
    }
};
