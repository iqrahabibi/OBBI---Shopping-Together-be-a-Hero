<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkouts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cart_id');
            $table->integer('qty');
            $table->string('invoice',20);
            $table->unsignedInteger('alamat_id');
            $table->string('tipe_pembayaran',100)->comment('cod or transfer');
            $table->integer('total_belanja')->comment('summary total_cart dan harga_kirim');
            $table->string('status',20)->comment('waiting, proccess etc');
            $table->timestamps();

            $table->foreign('cart_id')
                ->references('id')
                ->on('cart_onlines')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('alamat_id')
                ->references('id')
                ->on('user_alamats')
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
        Schema::dropIfExists('checkouts');
    }
}
