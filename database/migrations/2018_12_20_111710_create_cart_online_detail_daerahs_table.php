<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartOnlineDetailDaerahsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_online_detail_daerahs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cart_id');
            $table->string('kode',20);
            $table->unsignedInteger('barang_id');
            $table->string('varian',100)->nullable();
            $table->integer('qty');
            $table->integer('harga');
            $table->timestamps();

            $table->foreign('cart_id')
                ->references('id')
                ->on('cart_onlines')
                ->onDelete('cascade');
            
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
        Schema::dropIfExists('cart_online_detail_daerahs');
    }
}
