<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOMerchantBarangGrosirsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_merchant_barang_grosirs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_usaha',20);
            $table->unsignedInteger('barang_id');
            $table->unsignedInteger('varian_id');
            $table->integer('qty');
            $table->integer('harga_jual');
            $table->timestamps();

            $table->foreign('barang_id')
                ->references('id')
                ->on('barangs')
                ->onDelete('cascade');

            $table->foreign('varian_id')
                ->references('id')
                ->on('o_merchant_barang_varians')
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
        Schema::dropIfExists('o_merchant_barang_grosirs');
    }
}
