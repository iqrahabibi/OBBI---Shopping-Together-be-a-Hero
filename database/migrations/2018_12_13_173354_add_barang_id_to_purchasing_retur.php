<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBarangIdToPurchasingRetur extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchasing_order_returs', function (Blueprint $table) {
            $table->unsignedInteger('barang_id')->nullable()->after('purchasing_order_id');

            $table->foreign('barang_id')
                ->references('id')
                ->on('barangs')
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
        Schema::table('purchasing_order_returs', function ($table) {
            $table->dropForeign(['barang_id']);
            $table->dropColumn('barang_id');
        });
    }
}
