<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarangGrosirsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang_grosirs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('barang_id');
            $table->unsignedInteger('varian_id');
            $table->unsignedInteger('gudang_id');
            $table->integer('qty');
            $table->integer('harga_jual');
            $table->timestamps();

            $table->foreign('barang_id')
                ->references('id')
                ->on('barangs')
                ->onDelete('cascade');

            $table->foreign('varian_id')
                ->references('id')
                ->on('barang_varians')
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
        Schema::dropIfExists('barang_grosirs');
    }
}
