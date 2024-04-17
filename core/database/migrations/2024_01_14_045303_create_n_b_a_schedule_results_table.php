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
        Schema::create('nba_schedule_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('schedule_id')->nullable();
            $table->dateTime('schedule_startTime')->nullable();
            $table->dateTime('schedule_endedTime')->nullable();
            $table->integer('schedule_awayTeam_id')->nullable();
            $table->string('schedule_awayTeam_abbreviation')->nullable();
            $table->integer('schedule_homeTeam_id')->nullable();
            $table->string('schedule_homeTeam_abbreviation')->nullable();
            $table->bigInteger('schedule_venue_id')->nullable();
            $table->string('schedule_venue_name')->nullable();

            $table->string('schedule_venueAllegiance')->nullable();
            $table->string('schedule_scheduleStatus')->nullable();
            $table->string('schedule_originalStartTime')->nullable();
            $table->string('schedule_delayedOrPostponedReason')->nullable();
            $table->string('schedule_playedStatus')->nullable();
            $table->integer('schedule_attendance')->nullable();
            $table->json('schedule_officials')->nullable();
            $table->json('schedule_broadcasters')->nullable();
            $table->json('schedule_weather')->nullable();

            $table->string('score_currentQuarter')->nullable();
            $table->string('score_currentQuarterSecondsRemaining')->nullable();
            $table->string('score_currentIntermission')->nullable();
            $table->integer('score_awayScoreTotal')->nullable();
            $table->integer('score_homeScoreTotal')->nullable();
            $table->json('score_quarters')->nullable();

//            $table->json('references_teamReferences')->nullable();
//            $table->json('references_venueReferences')->nullable();

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
        Schema::dropIfExists('nba_schedule_results');
    }
};
