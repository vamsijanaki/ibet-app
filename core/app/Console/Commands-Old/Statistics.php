<?php

namespace App\Console\Commands;

use App\Models\APISetting;
use App\Models\GeneralSetting;
use App\Models\League;
use App\Models\PlayerStat;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class Statistics extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update player statistics';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $leagues = League::where( 'status', 1 )->get();
        foreach ( $leagues as $league ) {
            if ( $league->slug != 'nba' ) {
                continue;
            }
            try {
                $api_settings = APISetting::where( 'league_id', $league->id )->firstOrFail();
                $api_end      = $api_settings->players_stats_end_point . '?api_key=' . $api_settings->api_key;

                $teams = Team::where( 'league_id', $league->id )->get();
                foreach ( $teams as $team ) {
                    $api_url = preg_replace( "/(\{season\})/", $api_settings->season, $api_end );
                    $api_url = preg_replace( "/(\{year\})/", $api_settings->year, $api_url );
                    $api_url = preg_replace( "/(\{teamID\})/", $team->team_id, $api_url );

                    $ch = curl_init();
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch, CURLOPT_URL, $api_url );
                    $resp = curl_exec( $ch );
                    curl_close( $ch );

                    $result = json_decode( $resp );

                    $players = $result->players ?? [];
                    $count   = 0;
                    foreach ( $players as $player ) {
                        $table_name       = $league->slug . '_playerstats';
                        $playerstat_table = new PlayerStat();
                        $playerstat_table->setTable( $table_name );

                        $playerstat_table->updateOrCreate( [
                            "player_id" => $player->id,
                            "league_id" => $league->id
                        ], [
                            "full_name"        => $player->full_name,
                            "first_name"       => $player->first_name,
                            "last_name"        => $player->last_name,
                            "position"         => $player->position,
                            "primary_position" => $player->primary_position,
                            "jersey_number"    => $player->jersey_number ?? null,
                            "sr_id"            => $player->sr_id,
                            "reference"        => $player->reference,
                            "total"            => $player->total ? json_encode( $player->total ) : null,
                            "average"          => $player->average ? json_encode( $player->average ) : null,
                        ] );
                        $count ++;
                    }
                    Log::info( $team->name . " player Stats Totals = " . $count );
                }
            } catch ( \Exception $ex ) {
                Log::info( $ex->getMessage() );
                throw $ex;
            }
        }
    }
}
