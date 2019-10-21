<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasingOrderDetilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchasing_order_detils', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchasing_order_id');
            $table->unsignedInteger('barang_id');
            $table->unsignedInteger('barang_conversi_id');
            $table->integer('jumlah')->unsigned();
            $table->integer('harga')->unsigned();
            $table->timestamps();

            $table->foreign('purchasing_order_id')
                ->references('id')
                ->on('purchasing_orders')
                ->onDelete('cascade');

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
        Schema::dropIfExists('purchasing_order_detils');
    }
}
