<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKelurahanIdGudangs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gudangs', function (Blueprint $table) {
            $table->unsignedInteger('kelurahan_id')->nullable();

            $table->foreign('kelurahan_id')
                ->references('id')
                ->on('kelurahans')
                ->onUpdate('cascade')
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
        Schema::table('gudangs', function (Blueprint $table) {
            $table->dropForeign(['kelurahan_id']);
            $table->dropColumn('kelurahan_id');
        });
    }
}
