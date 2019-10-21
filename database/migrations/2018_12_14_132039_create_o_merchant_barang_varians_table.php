<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOMerchantBarangVariansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_merchant_barang_varians', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_usaha',20);
            $table->unsignedInteger('barang_id');
            $table->string('varian_barang');
            $table->timestamps();

            $table->foreign('barang_id')
                ->references('id')
                ->on('barangs')
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
        Schema::dropIfExists('o_merchant_barang_varians');
    }
}
