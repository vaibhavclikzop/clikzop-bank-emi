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
            $table->unsignedBigInteger('loan_banking_consent_id')->after('id');
            $table->string('mobile')->nullable()->after('micr');
            $table->string('current_balance')->nullable()->after('mobile');
            $table->string('current_od_limit')->nullable()->after('current_balance');
            $table->string('drawing_limit')->nullable()->after('current_od_limit');
            $table->string('name')->nullable()->after('raw_text_file');
            $table->string('nominee')->nullable()->after('name');
            $table->string('pan')->nullable()->after('nominee');
            $table->string('address')->nullable()->after('pan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
           Schema::table('loan_cibil_data_mst', function (Blueprint $table) {
            $table->dropColumn([
                'loan_banking_consent_id',
                'mobile',
                'current_balance',
                'current_od_limit',
                'drawing_limit',
                'name',
                'nominee',
                'pan',
                'address',
                
            ]);
        });
    }
};
