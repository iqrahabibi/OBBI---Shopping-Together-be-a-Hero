<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoFakturPurchasingOrderReturs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchasing_order_returs', function (Blueprint $table) {
            $table->string('no_faktur')->nullable()->after('purchasing_order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchasing_order_returs', function ($table) {
            $table->dropColumn('no_faktur');
        });
    }
}
