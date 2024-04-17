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
            $table->json('stats')->after('league_id')->nullable();
            $table->integer('player_one_adjustment')->after('stats')->nullable();
            $table->integer('player_two_adjustment')->after('player_one_adjustment')->nullable();
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
            $table->dropColumn('stats');
            $table->dropColumn('player_one_adjustment');
            $table->dropColumn('player_two_adjustment');
        });
    }
};
