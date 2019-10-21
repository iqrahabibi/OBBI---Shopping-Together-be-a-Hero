<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJualLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jual_licenses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('license_owner_id');
            $table->unsignedInteger('license_id');
            $table->date('tanggal_jual');
            $table->string('perolehan');
            $table->string('jenis_pembayaran');
            $table->timestamps();

            $table->foreign('license_owner_id')
                ->references('id')
                ->on('license_owners')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('license_id')
                ->references('id')
                ->on('licenses')
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
        Schema::dropIfExists('jual_licenses');
    }
}
