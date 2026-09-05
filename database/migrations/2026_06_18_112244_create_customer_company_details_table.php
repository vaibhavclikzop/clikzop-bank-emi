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
        Schema::create('customer_company_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('cin')->unique();
            $table->string('company_name')->nullable();
            $table->string('status')->nullable();
            $table->string('entity_class')->nullable();
            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('roc_code')->nullable();
            $table->string('whether_listed_or_not')->nullable();
            $table->decimal('paid_up_capital', 15, 2)->nullable();
            $table->decimal('authorised_capital', 15, 2)->nullable();
            $table->integer('number_of_members')->nullable();
            $table->date('date_of_incorporation')->nullable();
            $table->date('date_of_last_agm')->nullable();
            $table->date('date_of_balance_sheet')->nullable();
            $table->string('industry')->nullable();
            $table->text('sub_industry')->nullable();
            $table->longText('activity_group')->nullable();
            $table->longText('activity_class')->nullable();
            $table->longText('activity_sub_class')->nullable();
            $table->text('registered_address')->nullable();
            $table->text('alternative_address')->nullable();
            $table->string('email')->nullable();
            $table->boolean('alternate_source_data')->default(false);
            $table->text('pan_no')->nullable();
            $table->string('pan_last4')->nullable();
            $table->json('api_response')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('customer_company_directors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('customer_company_details')
                ->cascadeOnDelete();
            $table->bigInteger('customer_id');
            $table->string('din')->nullable();
            $table->string('pan')->nullable();
            $table->string('name')->nullable();
            $table->string('designation')->nullable();
            $table->date('dob')->nullable();
            $table->string('father_name')->nullable();
            $table->date('tenure_begin_date')->nullable();
            $table->date('tenure_end_date')->nullable();
            $table->text('address')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_company_details');
        Schema::dropIfExists('customer_company_directors');
    }
};
