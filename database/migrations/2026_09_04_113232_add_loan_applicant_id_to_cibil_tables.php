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
        Schema::table('loan_cibil_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_applicant_id')
                ->nullable()
                ->after('id');

            $table->foreign('loan_applicant_id')
                ->references('id')
                ->on('loan_applicants')
                ->onDelete('cascade');
        });

        Schema::table('loan_cibil_data_mst', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_applicant_id')
                ->nullable()
                ->after('id');

            $table->foreign('loan_applicant_id')
                ->references('id')
                ->on('loan_applicants')
                ->onDelete('cascade');
        });

        Schema::table('loan_cibil_enquiries', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_applicant_id')
                ->nullable()
                ->after('id');

            $table->foreign('loan_applicant_id')
                ->references('id')
                ->on('loan_applicants')
                ->onDelete('cascade');
        });

        Schema::table('loan_cibil_identifications', function (Blueprint $table) {
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
        foreach (['loan_cibil_accounts', 'loan_cibil_data_mst', 'loan_cibil_enquiries', 'loan_cibil_identifications'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['loan_applicant_id']);
                $table->dropColumn('loan_applicant_id');
            });
        }
    }
};
