<?php

namespace App\Console\Commands;

use App\Models\APISetting;
use App\Models\GeneralSetting;
use App\Models\League;
use App\Models\PlayerStat;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class PlayerStats extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:player-stat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update player stat';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $leagues = League::where( 'status', 1 )->get();

        $key      = env( "SPORTSAPP_KEY" );
        $password = env( "SPORTSAPP_PASSWORD" );

        foreach ( $leagues as $league ) {
            try {
                $api_settings = APISetting::where( 'league_id', $league->id )->firstOrFail();

                $api_end = $api_settings->players_stats_end_point;

                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_URL, $api_end );
                curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $ch, CURLOPT_ENCODING, "gzip" );
                curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                    "Authorization: Basic " . base64_encode( $key . ":" . $password )
                ] );
                $resp = curl_exec( $ch );
                curl_close( $ch );

                $result = json_decode( $resp );

                $playerStatsTotals = $result->playerStatsTotals ?? [];

                $table_name = $league->slug . '_playerstats';

                foreach ( $playerStatsTotals as $data ) {
                    $playerstat_table = new PlayerStat();
                    $playerstat_table->setTable( $table_name );

                    $playerstat_table->updateOrCreate( [
                        "player_id" => $data->player->id
                    ], [
                        "player_firstName"                => $data->player->firstName,
                        "player_lastName"                 => $data->player->lastName,
                        "player_primaryPosition"          => $data->player->primaryPosition,
                        "player_jerseyNumber"             => $data->player->jerseyNumber,
                        "player_currentTeam_id"           => $data->player->currentTeam->id ?? null,
                        "player_currentTeam_abbreviation" => $data->player->currentTeam->abbreviation ?? null,
                        "player_currentRosterStatus"      => $data->player->currentRosterStatus,
                        'team_id'                         => $data->team->id,
                        'team_abbreviation'               => $data->team->abbreviation,
                        'stats'                           => $data->stats ? json_encode( $data->stats ) : null,
                        'lastUpdatedOn'                   => Carbon::parse( $result->lastUpdatedOn )->subHour( 8 )
                    ] );
                }
                Log::info( $league->name . " player Stats Totals = " . count( $playerStatsTotals ) );
            } catch (\Exception $ex){
                Log::info( $ex->getMessage() );
            }
        }
    }
}
