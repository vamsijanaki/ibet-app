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
        Schema::create('nba_player_injuries', function (Blueprint $table) {
            $table->id();
            $table->integer('player_id')->nullable();
            $table->string('player_firstName')->nullable();
            $table->string('player_lastName')->nullable();
            $table->string('player_primaryPosition')->nullable();
            $table->string('player_jerseyNumber')->nullable();
            $table->integer('player_currentTeam_id')->nullable();
            $table->string('player_currentTeam_abbreviation')->nullable();
            $table->string('player_currentRosterStatus')->nullable();
            $table->string('player_currentInjury_description')->nullable();
            $table->string('player_currentInjury_playingProbability')->nullable();
            $table->dateTime('lastUpdatedOn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nba_player_injuries');
    }
};
