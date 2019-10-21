<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoFakturOMerchantPoMasuks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('o_merchant_po_masuks', function (Blueprint $table) {
            $table->string('no_faktur')->nullable()->after('o_merchant_po_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('o_merchant_po_masuks', function ($table) {
            $table->dropColumn('no_faktur');
        });
    }
}
