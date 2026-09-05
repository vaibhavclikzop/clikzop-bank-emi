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
        Schema::table('loan_cibil_data_mst', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('id');

            $table->string('file')->nullable()->after('customer_id');
            $table->string('json_file')->nullable()->after('file');
            $table->longText('raw_data')->nullable()->after('json_file');
            $table->string('control_number')->nullable()->after('raw_data');

            $table->date('report_date')->nullable()->after('control_number');
            $table->integer('cibil_score')->nullable()->after('report_date');

            $table->string('full_name')->nullable()->after('cibil_score');
            $table->text('date_of_birth')->nullable()->after('full_name');
            $table->string('gender', 20)->nullable()->after('date_of_birth');
            $table->integer('retry_count')->nullable()->after('gender');
            $table->dateTime('last_retry')->nullable()->after('retry_count');
            $table->timestamp('processed_at')->nullable()->after('last_retry');
            $table->timestamp('processing_started_at')->nullable()->after('processed_at');
            $table->enum('status', ["pending", "processing", "complete", "error"])->default('pending')->after('processing_started_at');
            $table->longText('message')->nullable()->after('status');
            $table->integer('bank_id')->nullable()->change();
            $table->integer('loan_type_id')->nullable()->change();
            $table->integer('cibil_status_id')->nullable()->change();
            $table->string('loan_account_no')->nullable()->change();
            $table->string('request_id')->nullable()->after('processing_started_at');
            $table->string('currentCredit')->nullable()->after('request_id');
            $table->string('creditUsed')->nullable()->after('currentCredit');
            $table->string('OldestCreditAccountPeriod')->nullable()->after('creditUsed');
            $table->string('Inquires')->nullable()->after('OldestCreditAccountPeriod');
            $table->string('OnTimePaymentHistory')->nullable()->after('Inquires');
            $table->string('CreditCardUtilization')->nullable()->after('OnTimePaymentHistory');
            $table->string('CreditMix')->nullable()->after('CreditCardUtilization');
            $table->string('serialNumber')->nullable()->after('CreditMix');
            $table->string('reportUrl')->nullable()->after('serialNumber');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_cibil_data_mst', function (Blueprint $table) {
            $table->dropColumn([
                'customer_id',
                'file',
                'control_number',
                'report_date',
                'cibil_score',
                'full_name',
                'date_of_birth',
                'gender',
                'retry_count',
                'last_retry',
                'processed_at',
                'processing_started_at',
                'status',
                'message',
                'raw_data',
                "json_file",
                "request_id",
                "currentCredit",
                "creditUsed",
                "OldestCreditAccountPeriod",
                "Inquires",
                "OnTimePaymentHistory",
                "CreditCardUtilization",
                "CreditMix",
                "serialNumber",
                "reportUrl",
            ]);
        });
    }
};
