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
        Schema::create('tennis_tournaments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tournament_key');
            $table->string('tournament_name');
            $table->unsignedBigInteger('event_type_key');
            $table->string('event_type_type');
            $table->string('tournament_surface');
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
        Schema::dropIfExists('tennis_tournaments');
    }
};
