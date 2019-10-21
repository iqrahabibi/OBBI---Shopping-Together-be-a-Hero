<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTargetDonasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_donasis', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tipe_donasi_id');
            $table->unsignedInteger('agama_id');
            $table->string('nama_target_donasi',120);
            $table->timestamps();

            $table->foreign('tipe_donasi_id')
                ->references('id')
                ->on('tipe_donasis')
                ->onDelete('cascade')
                ->onUpdate('cascade');

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
        Schema::dropIfExists('target_donasis');
    }
}
