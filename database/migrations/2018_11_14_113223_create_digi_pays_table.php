<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDigiPaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digi_pays', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('finance_id')->nullable();
            $table->string('invoice',20)->nullable();
            $table->integer('awal')->unsigned();
            $table->integer('jumlah')->unisgned();
            $table->integer('akhir')->unsigned();
            $table->string('trxid')->unique()->nullable();
            $table->string('notes')->nullable();
            $table->string('phone',15)->unique()->nullable();
            $table->string('tipe_token')->nullable();
            $table->string('token_number')->nullable();
            $table->unsignedInteger('kode')->unique();
            $table->tinyinteger('valid')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('finance_id')
                ->references('id')
                ->on('finances')
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
        Schema::dropIfExists('digi_pays');
    }
}
