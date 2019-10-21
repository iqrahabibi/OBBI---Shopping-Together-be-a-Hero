<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartOnlinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_onlines', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_invoice',10);
            $table->unsignedInteger('belanja_id');
            $table->unsignedInteger('user_id');
            $table->integer('total_qty');
            $table->integer('total_belanja');
            $table->string('status');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('cart_onlines');
    }
}
