<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOMerchantBarangPromosisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_merchant_barang_promosis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_usaha',20);
            $table->unsignedInteger('om_barang_kategori_id');
            $table->string('judul',150);
            $table->integer('min_total_harga_pesanan');
            $table->double('jumlah_diskon')->nullable();
            $table->double('diskon')->nullable();
            $table->double('max_jumlah_diskon')->nullable();
            $table->tinyInteger('kelipatan')->default(0);
            $table->date('tanggal_aktif')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_akhir')->nullable();
            $table->string('hari');
            $table->timestamps();

            $table->foreign('om_barang_kategori_id')
                ->references('id')
                ->on('o_merchant_barang_promosi_kategoris')
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
        Schema::dropIfExists('o_merchant_barang_promosis');
    }
}
