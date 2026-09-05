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
        Schema::table('loan_itr_filing_history', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_applicant_id')
                ->nullable()
                ->after('id');

            $table->foreign('loan_applicant_id')
                ->references('id')
                ->on('loan_applicants')
                ->onDelete('cascade');
        });

        Schema::table('loan_itr_out_standing_demands', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_applicant_id')
                ->nullable()
                ->after('id');

            $table->foreign('loan_applicant_id')
                ->references('id')
                ->on('loan_applicants')
                ->onDelete('cascade');
        });

        Schema::table('loan_itr_profile', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_applicant_id')
                ->nullable()
                ->after('id');

            $table->foreign('loan_applicant_id')
                ->references('id')
                ->on('loan_applicants')
                ->onDelete('cascade');
        });

        Schema::table('loan_itr_yearly', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_applicant_id')
                ->nullable()
                ->after('id');

            $table->foreign('loan_applicant_id')
                ->references('id')
                ->on('loan_applicants')
                ->onDelete('cascade');
        });
        Schema::table('loan_itr_yearly_financial', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_applicant_id')
                ->nullable()
                ->after('id');

            $table->foreign('loan_applicant_id')
                ->references('id')
                ->on('loan_applicants')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['loan_itr_filing_history', 'loan_itr_out_standing_demands', 'loan_itr_profile', 'loan_itr_yearly', 'loan_itr_yearly_financial'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['loan_applicant_id']);
                $table->dropColumn('loan_applicant_id');
            });
        }
    }
};
