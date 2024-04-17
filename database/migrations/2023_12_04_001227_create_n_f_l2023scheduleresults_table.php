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
        Schema::create('nfl_schedule_results', function (Blueprint $table) {
            $table->id();
            $table->integer('team_id')->nullable();
            $table->string('team_abbreviation')->nullable();
            $table->bigInteger('schedule_id')->nullable();
            $table->integer('schedule_week')->nullable();
            $table->dateTime('schedule_startTime')->nullable();
            $table->dateTime('schedule_endedTime')->nullable();
            $table->integer('homeTeam_id')->nullable();
            $table->string('homeTeam_abbreviation')->nullable();
            $table->bigInteger('venue_id')->nullable();
            $table->string('venue_name')->nullable();
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
        Schema::dropIfExists('nfl_schedule_results');
    }
};
