<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKaryawansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('divisi_id');
            $table->unsignedInteger('jabatan_id');
            $table->string('nama_karyawan',20);
            $table->string('alamat',20);
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir',20);
            $table->string('handphone1',13);
            $table->string('handphone2',13);
            $table->date('tanggal_masuk')->nullable();
            $table->timestamps();

           
             $table->foreign('divisi_id')
                 ->references('id')
                 ->on('divisis')
                 ->onDelete('cascade')
                 ->onUpdate('cascade');

                 $table->foreign('jabatan_id')
                 ->references('id')
                 ->on('jabatans')
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
        Schema::dropIfExists('karyawans');
    }
}
