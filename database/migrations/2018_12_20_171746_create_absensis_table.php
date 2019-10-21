<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsensisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('karyawan_id');
            $table->date('tanggal_absen');
            $table->string('absen');
            // $table->datetime('absen_masuk');
            // $table->datetime('absen_keluar');
            $table->timestamps();

            $table->foreign('karyawan_id')
                 ->references('id')
                 ->on('karyawans')
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
        Schema::dropIfExists('absensis');
    }
}
