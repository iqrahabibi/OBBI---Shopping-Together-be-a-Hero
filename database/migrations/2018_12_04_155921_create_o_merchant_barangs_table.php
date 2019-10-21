<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOMerchantBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_merchant_barangs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_omerchant',20)->index();
            $table->unsignedInteger('barang_id');
            $table->unsignedInteger('barang_conversi_id');
            $table->integer('jumlah');
            $table->string('periode');
            $table->tinyinteger('publish')->default(1);
            $table->integer('harga_satuan');
            $table->timestamps();

            $table->foreign('barang_id')
                ->references('id')
                ->on('barangs')
                ->onDelete('cascade');

            $table->foreign('barang_conversi_id')
                ->references('id')
                ->on('barang_conversis')
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
        Schema::dropIfExists('o_merchant_barangs');
    }
}
