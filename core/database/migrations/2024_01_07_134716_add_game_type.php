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
        Schema::table('games', function (Blueprint $table) {
            $table->integer('game_type_id')->after('league_id')->nullable();
            $table->integer('player_one_id')->after('team_two_id')->nullable();
            $table->integer('player_two_id')->after('player_one_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('game_type_id');
            $table->dropColumn('player_one_id');
            $table->dropColumn('player_two_id');
        });
    }
};
