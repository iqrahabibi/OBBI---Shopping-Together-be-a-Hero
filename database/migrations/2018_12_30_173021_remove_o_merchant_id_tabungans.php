<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveOMerchantIdTabungans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tabungans', function ($table) {
            $table->dropForeign(['o_merchant_id']);
            $table->dropColumn('o_merchant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tabungans', function (Blueprint $table) {
            $table->unsignedInteger('o_merchant_id')->nullable()->after('id');

            $table->foreign('o_merchant_id')
                ->references('id')
                ->on('o_merchants')
                ->onDelete('cascade');
        });
    }
}
