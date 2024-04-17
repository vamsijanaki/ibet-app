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
        Schema::create('nba_daily_player_gamelogs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('game_id')->nullable();
            $table->dateTime('game_startTime')->nullable();
            $table->string('game_awayTeamAbbreviation')->nullable();
            $table->string('game_homeTeamAbbreviation')->nullable();

            $table->integer('player_id')->nullable();
            $table->string('player_firstName')->nullable();
            $table->string('player_lastName')->nullable();
            $table->string('player_position')->nullable();
            $table->string('player_jerseyNumber')->nullable();

            $table->integer('team_id')->nullable();
            $table->string('team_abbreviation')->nullable();

            $table->json('stats')->nullable();

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
        Schema::dropIfExists('nba_daily_player_gamelogs');
    }
};
