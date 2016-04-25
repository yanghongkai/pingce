<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddObjectSubjectScorerPaper extends Migration
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
            $table->float('object_grade')->after('detail_xml');
            $table->float('subject_grade')->after('detail_xml');
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
            $table->dropColumn(['object_grade','subject_grade']);
        });
    }
}
