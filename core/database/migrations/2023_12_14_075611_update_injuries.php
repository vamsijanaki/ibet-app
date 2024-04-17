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
    public function up() {
        Schema::table( 'nfl_player_injuries', function ( Blueprint $table ) {
            $table->string('player_currentInjury_playingProbability')->nullable()->after('player_currentInjury_description');
            $table->dateTime('lastUpdatedOn')->nullable()->after('player_currentInjury_playingProbability');
        } );
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nfl_player_injuries', function (Blueprint $table) {
            $table->dropColumn('player_currentInjury_playingProbability');
            $table->dropColumn('lastUpdatedOn');
        });
    }
};
