<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCheckoutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->string('tipe_belanja',20)->comment('nasional or lokal')->nullable(); 
            $table->string('kurir',150)->nullable();
            $table->string('produk',20)->nullable();
            $table->integer('harga_kirim')->nullable();
            $table->timestamp('expired_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropColumn('tipe_belanja');
            $table->dropColumn('kurir');
            $table->dropColumn('produk');
            $table->dropColumn('harga_kirim');
            $table->dropColumn('expired_at');
        });
    }
}
