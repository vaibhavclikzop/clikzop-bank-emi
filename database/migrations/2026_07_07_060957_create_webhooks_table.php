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
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("loan_gst_details_id");
            $table->string('provider', 50)->nullable();
            $table->string('event')->nullable();
            $table->string('request_id')->nullable()->index();
            $table->string('method', 10)->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->json('headers')->nullable();
            $table->text('payload');
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->boolean('processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->enum("status", ["success", "failed"])->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
