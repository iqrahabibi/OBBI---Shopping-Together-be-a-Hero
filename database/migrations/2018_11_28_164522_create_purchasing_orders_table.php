<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasingOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchasing_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('suplier_id');
            $table->unsignedInteger('gudang_id');
            $table->string('nomor_po');
            $table->date('tanggal_po');
            $table->date('tanggal_po_masuk')->nullable();
            $table->date('tanggal_batas_retur')->nullable();
            $table->integer('total')->unsigned();
            $table->tinyinteger('match')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('suplier_id')
                ->references('id')
                ->on('supliers')
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
        Schema::dropIfExists('purchasing_orders');
    }
}
