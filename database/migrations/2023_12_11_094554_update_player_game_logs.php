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
        Schema::table('nfl_weekly_player_gamelogs', function (Blueprint $table) {
            $table->integer('player_id')->nullable()->after('game_homeTeamAbbreviation');
            $table->string('player_firstName')->nullable()->after('player_id');
            $table->string('player_lastName')->nullable()->after('player_firstName');
            $table->string('player_position')->nullable()->after('player_lastName');
            $table->string('player_jerseyNumber')->nullable()->after('player_position');

            $table->integer('team_id')->nullable()->after('player_jerseyNumber');
            $table->string('team_abbreviation')->nullable()->after('team_id');

            $table->longText('stats')->nullable()->after('team_abbreviation');

            $table->dateTime('lastUpdatedOn')->nullable()->after('stats');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nfl_weekly_player_gamelogs', function (Blueprint $table) {
            $table->dropColumn('player_id');
            $table->dropColumn('player_firstName');
            $table->dropColumn('player_lastName');
            $table->dropColumn('player_position');
            $table->dropColumn('player_jerseyNumber');
            $table->dropColumn('team_id');
            $table->dropColumn('team_abbreviation');
            $table->dropColumn('stats');
            $table->dropColumn('lastUpdatedOn');
        });
    }
};
