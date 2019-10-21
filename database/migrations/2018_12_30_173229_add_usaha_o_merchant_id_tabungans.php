<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsahaOMerchantIdTabungans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tabungans', function (Blueprint $table) {
            $table->unsignedInteger('usaha_o_merchant_id')->nullable()->after('id');

            $table->foreign('usaha_o_merchant_id')
                ->references('id')
                ->on('usaha_o_merchants')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tabungans', function ($table) {
            $table->dropForeign(['usaha_o_merchant_id']);
            $table->dropColumn('usaha_o_merchant_id');
        });
    }
}
