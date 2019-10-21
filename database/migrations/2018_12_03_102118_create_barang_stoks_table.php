<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarangStoksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang_stoks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('barang_id');
            $table->unsignedInteger('barang_conversi_id');
            $table->unsignedInteger('gudang_id');
            $table->integer('jumlah')->unsigned();
            $table->string('periode');
            $table->tinyinteger('publish')->unsigned();
            $table->integer('harga_satuan')->unsigned();
            $table->timestamps();

            $table->foreign('barang_id')
                ->references('id')
                ->on('barangs')
                ->onDelete('cascade');

            $table->foreign('barang_conversi_id')
                ->references('id')
                ->on('barang_conversis')
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
        Schema::dropIfExists('barang_stoks');
    }
}
