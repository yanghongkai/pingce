<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountScorerPaper extends Migration
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
            $table->integer('count')->after('detail_xml');
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
            $table->dropColumn('count');
        });
    }
}
