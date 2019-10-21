<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('kriteria_license_id');
            $table->unsignedInteger('kelurahan_id');
            $table->string('nomor_sertifikat',100);
            $table->string('nomor_kartu',100);
            $table->string('file_perjanjian',191)->nullable();
            $table->string('file_sertifikat',191)->nullable();
            $table->timestamps();

            $table->foreign('kriteria_license_id')
                ->references('id')
                ->on('kriteria_licenses')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('kelurahan_id')
                ->references('id')
                ->on('kelurahans')
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
        Schema::dropIfExists('licenses');
    }
}
