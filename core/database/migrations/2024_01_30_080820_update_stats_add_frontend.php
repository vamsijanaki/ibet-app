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
        Schema::table('stats', function (Blueprint $table) {
//            $table->string('frontend_name')->after('display_name')->nullable();
            $table->integer('sort_order')->default(0)->after('frontend_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stats', function (Blueprint $table) {
//            $table->dropColumn('frontend_name');
            $table->dropColumn('sort_order');
        });
    }
};
