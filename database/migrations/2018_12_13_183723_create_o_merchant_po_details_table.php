<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOMerchantPoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_merchant_po_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('o_merchant_po_id');
            $table->unsignedInteger('barang_grosir_id');
            $table->integer('jumlah');
            $table->integer('harga');
            $table->timestamps();

            $table->foreign('o_merchant_po_id')
                ->references('id')
                ->on('o_merchant_pos')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('barang_grosir_id')
                ->references('id')
                ->on('barang_grosirs')
                ->onDelete('cascade')
                ->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('o_merchant_po_details');
    }
}
