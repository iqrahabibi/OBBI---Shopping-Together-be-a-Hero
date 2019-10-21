<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFlagCartOnline extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_onlines', function (Blueprint $table) {
            $table->dropForeign(['nasional_id']);
            $table->dropForeign(['daerah_id']);
            $table->dropColumn('nasional_id');
            $table->dropColumn('daerah_id');
            $table->tinyInteger('flag')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_onlines', function (Blueprint $table) {
            $table->dropColumn('flag');
            $table->unsignedInteger('nasional_id')->nullable();
            $table->unsignedInteger('daerah_id')->nullable();

            $table->foreign('nasional_id')
                ->references('id')
                ->on('barang_inventories')
                ->onDelete('cascade');

            $table->foreign('daerah_id')
                ->references('id')
                ->on('o_merchant_barang_inventories')
                ->onDelete('cascade');
        });
    }
}
