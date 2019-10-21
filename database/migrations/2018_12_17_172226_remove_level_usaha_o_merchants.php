<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveLevelUsahaOMerchants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usaha_o_merchants', function ($table) {
            $table->dropColumn('level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usaha_o_merchants', function (Blueprint $table) {
            $table->tinyinteger('level')->unsigned()->nullable()->after('tanggal_keluar');
        });
    }
}
