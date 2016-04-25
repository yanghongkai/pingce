<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDetailsScorerPaper extends Migration
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
            $table->longText('detail_xml')->after('grade');
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
            $table->dropColumn('detail_xml');
        });
    }
}
