<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubmitSocrerPaper extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scorer_paper', function (Blueprint $table) {
            //
            $table->integer('submit')->default(0)->after('detail_xml');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scorer_paper', function (Blueprint $table) {
            //
            $table->dropColumn('submit');
        });
    }
}
