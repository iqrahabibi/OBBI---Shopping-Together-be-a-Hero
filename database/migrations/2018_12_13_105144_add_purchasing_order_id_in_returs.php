<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPurchasingOrderIdInReturs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchasing_order_returs', function (Blueprint $table) {
            $table->unsignedInteger('purchasing_order_id')->nullable()->after('id');
            $table->dropForeign(['purchasing_order_masuk_id']);
            $table->dropColumn('purchasing_order_masuk_id');

            $table->foreign('purchasing_order_id')
                ->references('id')
                ->on('purchasing_orders')
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
            $table->dropForeign(['purchasing_order_id']);
            $table->dropColumn('purchasing_order_id');
        });
    }
}
