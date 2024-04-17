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
        Schema::create('api_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id');
            $table->string('players_end_point')->nullable();
            $table->string('players_injury_end_point')->nullable();
            $table->string('players_stats_end_point')->nullable();
            $table->string('players_game_log_end_point')->nullable();
            $table->string('teams_game_log_end_point')->nullable();
            $table->string('schedule_result_end_point')->nullable();
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
        Schema::dropIfExists('api_settings');
    }
};
