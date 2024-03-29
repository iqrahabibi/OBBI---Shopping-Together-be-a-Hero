<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUniqueKodeDigiPays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('digi_pays', function(Blueprint $table)
        {
            $table->dropUnique('digi_pays_kode_unique');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('digi_pays', function(Blueprint $table)
        {
            //Put the index back when the migration is rolled back
            // $table->unique('kode');

        });
    }
}
