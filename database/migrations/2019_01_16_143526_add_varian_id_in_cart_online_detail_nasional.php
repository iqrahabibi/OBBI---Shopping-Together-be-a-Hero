<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVarianIdInCartOnlineDetailNasional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_online_detail_nasionals', function (Blueprint $table) {
            $table->dropColumn('varian');
            $table->unsignedInteger('varian_id')->nullable();

            $table->foreign('varian_id')
                ->references('id')
                ->on('barang_varians')
                ->onUpdate('cascade')
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
        Schema::table('cart_online_detail_nasionals', function (Blueprint $table) {
            $table->dropForeign(['varian_id']);
            $table->dropColumn('varian_id');
            $table->string('varian')->nullable();
        });
    }
}
