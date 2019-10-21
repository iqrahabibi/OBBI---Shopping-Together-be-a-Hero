<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarangStokOpnameDetilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang_stok_opname_detils', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('barang_stok_opname_id');
            $table->unsignedInteger('barang_stok_id');
            $table->integer('jumlah')->unsigned();
            $table->string('type');
            $table->timestamps();

            $table->foreign('barang_stok_opname_id')
                ->references('id')
                ->on('barang_stok_opnames')
                ->onDelete('cascade');

            $table->foreign('barang_stok_id')
                ->references('id')
                ->on('barang_stoks')
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
        Schema::dropIfExists('barang_stok_opname_detils');
    }
}
