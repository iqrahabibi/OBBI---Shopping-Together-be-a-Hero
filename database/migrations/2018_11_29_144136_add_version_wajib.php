<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVersionWajib extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('versions', function (Blueprint $table) {
            $table->string('code_baru',100)->nullable()->after('name');
            $table->tinyInteger('wajib')->nullable()->after('code_baru');
            $table->string('version',10)->nullable()->after('wajib');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('versions', function ($table) {
            $table->dropColumn('code_baru');
            $table->dropColumn('wajib');
            $table->dropColumn('version');
        });
    }
}
