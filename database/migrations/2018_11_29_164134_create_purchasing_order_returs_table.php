<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasingOrderRetursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchasing_order_returs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchasing_order_masuk_id');
            $table->integer('jumlah')->unsigned();
            $table->timestamps();

            $table->foreign('purchasing_order_masuk_id')
                ->references('id')
                ->on('purchasing_order_masuks')
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
        Schema::dropIfExists('purchasing_order_returs');
    }
}
