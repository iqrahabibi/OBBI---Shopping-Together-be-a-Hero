<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsahaOMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usaha_o_merchants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('usaha_id');
            $table->unsignedInteger('o_merchant_id');
            $table->string('kode',10);
            $table->string('type',2);
            $table->integer('modal')->unsigned();
            $table->double('porsi');
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->tinyinteger('level')->unsigned();
            $table->tinyinteger('valid')->unsigned();
            $table->timestamps();

            $table->foreign('usaha_id')
                ->references('id')
                ->on('usahas')
                ->onDelete('cascade');

            $table->foreign('o_merchant_id')
                ->references('id')
                ->on('o_merchants')
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
        Schema::dropIfExists('usaha_o_merchants');
    }
}
