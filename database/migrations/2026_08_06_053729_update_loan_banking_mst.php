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
        Schema::table('loan_banking_mst', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('id');

            $table->string('file')->nullable()->after('customer_id');
            $table->string('json_file')->nullable()->after('file');
            $table->longText('raw_text_file')->nullable()->after('json_file');
            $table->string('bank_name')->nullable()->after('raw_text_file');
            $table->string('statement_from')->nullable()->after('bank_name');
            $table->string('statement_to')->nullable()->after('statement_from');
            $table->string('account_type')->nullable()->after('statement_to');
            $table->string('branch')->nullable()->after('account_type');
            $table->string('ifsc')->nullable()->after('branch');
            $table->string('micr')->nullable()->after('ifsc');
            $table->string('opening_balance')->nullable()->after('micr');
            $table->string('closing_balance')->nullable()->after('opening_balance');
            $table->string('currency')->nullable()->after('closing_balance');
            $table->string('total_transactions')->nullable()->after('currency');
            $table->string('total_debit')->nullable()->after('total_transactions');
            $table->string('total_credit')->nullable()->after('total_debit');

            $table->integer('retry_count')->nullable()->after('total_credit');
            $table->dateTime('last_retry')->nullable()->after('retry_count');
            $table->timestamp('processed_at')->nullable()->after('last_retry');
            $table->timestamp('processing_started_at')->nullable()->after('processed_at');
            $table->enum('status', ["pending", "processing", "complete", "error"])->default('pending')->after('processing_started_at');
            $table->longText('message')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_banking_mst', function (Blueprint $table) {
            $table->dropColumn([
                'customer_id',
                'file',
                'json_file',
                'raw_text_file',
                'bank_name',
                'statement_from',
                'statement_to',
                'account_type',
                'branch',
                'ifsc',
                'micr',
                'opening_balance',
                'closing_balance',
                'currency',
                'total_debit',
                'total_credit',
                'retry_count',
                'last_retry',
                'processed_at',
                'processing_started_at',
                'status',
                'message',
            ]);
        });
    }
};
