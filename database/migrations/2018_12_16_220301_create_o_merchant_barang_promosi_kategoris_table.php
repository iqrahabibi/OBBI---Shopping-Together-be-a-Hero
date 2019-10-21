<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOMerchantBarangPromosiKategorisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_merchant_barang_promosi_kategoris', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_usaha',20);
            $table->string('nama_kategori',100);
            $table->text('deskripsi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('o_merchant_barang_promosi_kategoris');
    }
}
