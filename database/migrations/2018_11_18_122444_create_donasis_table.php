<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donasis', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('target_donasi_id');
            $table->unsignedInteger('detail_user_id');
            $table->integer('awal')->unsigned()->default(0);
            $table->integer('jumlah')->unisgned()->default(0);
            $table->integer('akhir')->unsigned()->default(0);
            $table->timestamps();

            $table->foreign('target_donasi_id')
                ->references('id')
                ->on('target_donasis')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('detail_user_id')
                ->references('id')
                ->on('detail_users')
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
        Schema::dropIfExists('donasis');
    }
}
