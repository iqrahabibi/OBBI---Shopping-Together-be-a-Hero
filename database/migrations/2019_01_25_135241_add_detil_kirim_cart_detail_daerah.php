<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDetilKirimCartDetailDaerah extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_online_detail_daerahs', function (Blueprint $table) {
            $table->string('detail_kirim')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_online_detail_daerahs', function (Blueprint $table) {
            $table->dropColumn('detail_kirim');
        });
    }
}
