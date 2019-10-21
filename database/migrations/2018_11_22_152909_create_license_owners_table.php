<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLicenseOwnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('license_owners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nik',100);
            $table->string('nama_depan',191);
            $table->string('nama_tengah',191)->nullable();
            $table->string('nama_belakang',191)->nullable();
            $table->string('nama_lengkap',191);
            $table->date('tanggal_lahir');
            $table->unsignedInteger('agama_id');
            $table->string('status_pernikahan',20);
            $table->string('jenis_kelamin',10);
            $table->string('alamat',100);
            $table->string('rt',5);
            $table->string('rw',5);
            $table->string('no_telp',15);
            $table->string('no_telp_2',15);
            $table->unsignedInteger('pewaris_id')->nullable();
            $table->string('file_ktp',191)->nullable();
            $table->integer('valid')->unsigned()->default(1);
            $table->timestamps();

            $table->foreign('agama_id')
                ->references('id')
                ->on('agamas')
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
        Schema::dropIfExists('license_owners');
    }
}
