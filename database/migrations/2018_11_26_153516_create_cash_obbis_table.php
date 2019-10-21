<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashObbisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_obbis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipe',100);
            $table->string('kode',10);
            $table->unsignedInteger('user_id');
            $table->integer('jumlah')->unsigned();
            $table->tinyinteger('cash')->unsigned();
            $table->string('status',6);
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
        Schema::dropIfExists('cash_obbis');
    }
}
