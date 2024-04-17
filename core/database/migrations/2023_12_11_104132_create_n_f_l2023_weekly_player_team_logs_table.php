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
        Schema::create('nfl_weekly_team_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('game_id')->nullable();
            $table->string('game_week')->nullable();
            $table->dateTime('game_startTime')->nullable();
            $table->string('game_awayTeamAbbreviation')->nullable();
            $table->string('game_homeTeamAbbreviation')->nullable();

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
        Schema::dropIfExists('nfl_weekly_team_logs');
    }
};
