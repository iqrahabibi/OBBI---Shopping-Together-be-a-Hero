<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartOnlineDetailNasionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_online_detail_nasionals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cart_id');
            $table->unsignedInteger('barang_id');
            $table->unsignedInteger('gudang_id');
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

            $table->foreign('gudang_id')
                ->references('id')
                ->on('gudangs')
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
        Schema::dropIfExists('cart_online_detail_nasionals');
    }
}
