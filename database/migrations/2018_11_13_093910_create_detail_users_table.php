<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('kelurahan_id');
            $table->string('agama',20);
            $table->unsignedInteger('agama_id');
            $table->string('alamat');
            $table->string('phone',15);
            $table->tinyinteger('valid')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            
            $table->foreign('agama_id')
                ->references('id')
                ->on('agamas')
                ->onDelete('cascade');

                
            $table->foreign('kelurahan_id')
                ->references('id')
                ->on('kelurahans')
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
        Schema::dropIfExists('detail_users');
    }
}
