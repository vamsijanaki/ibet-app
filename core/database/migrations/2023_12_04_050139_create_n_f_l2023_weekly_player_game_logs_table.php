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
        Schema::create('nfl_weekly_player_gamelogs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('game_id')->nullable();
            $table->integer('game_week')->nullable();
            $table->dateTime('game_startTime')->nullable();
            $table->string('game_awayTeamAbbreviation')->nullable();
            $table->string('game_homeTeamAbbreviation')->nullable();
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
        Schema::dropIfExists('nfl_weekly_player_gamelogs');
    }
};
