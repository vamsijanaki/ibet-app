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
        Schema::create('tennis_headtohead', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('league_id');
            $table->unsignedBigInteger('schedule_id');
            $table->date('event_date');
            $table->time('event_time');
            $table->string('event_first_player');
            $table->unsignedBigInteger('first_player_key');
            $table->string('event_second_player');
            $table->unsignedBigInteger('second_player_key');
            $table->string('event_final_result');
            $table->string('event_game_result');
            $table->string('event_serve')->nullable();
            $table->string('event_winner');
            $table->string('event_status');
            $table->string('event_type_type');
            $table->string('tournament_name');
            $table->unsignedBigInteger('tournament_key');
            $table->string('tournament_round');
            $table->string('tournament_season');
            $table->boolean('event_live');
            $table->string('event_first_player_logo')->nullable();
            $table->string('event_second_player_logo')->nullable();
            $table->boolean('event_qualification');
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
        Schema::dropIfExists('tennis_headtohead');
    }
};
