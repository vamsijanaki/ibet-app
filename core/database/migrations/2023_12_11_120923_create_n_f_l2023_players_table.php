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
        Schema::create('nfl_players', function (Blueprint $table) {
            $table->id();
            $table->integer('player_id')->nullable();
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('primaryPosition')->nullable();

            $table->text('alternatePositions')->nullable();

            $table->integer('jerseyNumber')->nullable();

            $table->integer('currentTeam_id')->nullable();
            $table->string('currentTeam_abbreviation')->nullable();

            $table->string('currentRosterStatus')->nullable();

            $table->text('currentInjury')->nullable();

            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->date('birthDate')->nullable();
            $table->integer('age')->nullable();
            $table->string('birthCity')->nullable();
            $table->string('birthCountry')->nullable();
            $table->string('rookie')->nullable();
            $table->string('highSchool')->nullable();
            $table->string('college')->nullable();

            $table->text('handedness')->nullable();

            $table->string('officialImageSrc')->nullable();

            $table->text('socialMediaAccounts')->nullable();

            $table->text('currentContractYear')->nullable();

            $table->text('drafted')->nullable();

            $table->text('externalMappings')->nullable();

            $table->text('teamAsOfDate')->nullable();

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
        Schema::dropIfExists('nfl_players');
    }
};
