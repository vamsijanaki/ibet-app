<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nfl_schedule_results', function (Blueprint $table) {
            $table->string('currentQuarter')->nullable()->after('venue_name');
            $table->string('currentQuarterSecondsRemaining')->nullable()->after('currentQuarter');
            $table->string('currentIntermission')->nullable()->after('currentQuarterSecondsRemaining');
            $table->string('teamInPossession')->nullable()->after('currentIntermission');
            $table->string('currentDown')->nullable()->after('teamInPossession');
            $table->string('currentYardsRemaining')->nullable()->after('currentDown');
            $table->string('lineOfScrimmage')->nullable()->after('currentYardsRemaining');
            $table->integer('awayScoreTotal')->nullable()->after('lineOfScrimmage');
            $table->integer('homeScoreTotal')->nullable()->after('awayScoreTotal');
            $table->longText('quarters')->nullable()->after('homeScoreTotal');
            $table->dateTime('lastUpdatedOn')->nullable()->after('quarters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nfl_schedule_results', function (Blueprint $table) {
            $table->dropColumn('currentQuarter');
            $table->dropColumn('currentQuarterSecondsRemaining');
            $table->dropColumn('currentIntermission');
            $table->dropColumn('teamInPossession');
            $table->dropColumn('currentDown');
            $table->dropColumn('currentYardsRemaining');
            $table->dropColumn('lineOfScrimmage');
            $table->dropColumn('awayScoreTotal');
            $table->dropColumn('homeScoreTotal');
            $table->dropColumn('quarters');
            $table->dropColumn('lastUpdatedOn');
        });
    }
};
