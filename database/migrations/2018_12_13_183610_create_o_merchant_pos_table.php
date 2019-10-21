<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOMerchantPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('o_merchant_pos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode',10);
            $table->string('nomor_po');
            $table->unsignedInteger('gudang_id');
            $table->date('tanggal');
            $table->tinyinteger('match')->default(0);
            $table->integer('total')->nullable();
            $table->integer('total_masuk')->nullable();
            $table->date('tanggal_po_masuk')->nullable();
            $table->date('tanggal_batas_retur')->nullable();
            $table->string('status', 15);
            $table->timestamps();

            $table->foreign('gudang_id')
                ->references('id')
                ->on('gudangs')
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
        Schema::dropIfExists('o_merchant_pos');
    }
}
