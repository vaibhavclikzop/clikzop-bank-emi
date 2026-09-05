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
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('customer_gstr1_details');
        Schema::dropIfExists('customer_gstr3b_details');
        Schema::dropIfExists('customer_gstr3b_itc_details');
        Schema::dropIfExists('customer_gstr3b_supply_details');
        Schema::dropIfExists('customer_gst_details');
        Schema::dropIfExists('customer_gst_detail_fillings');
        Schema::dropIfExists('customer_itr_filing_history');
        Schema::dropIfExists('customer_itr_out_standing_demands');
        Schema::dropIfExists('customer_itr_profile');
        Schema::dropIfExists('customer_itr_yearly');
        Schema::dropIfExists('customer_itr_yearly_financial');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
