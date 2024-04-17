<?php

namespace App\Console\Commands;

use App\Models\APISetting;
use App\Models\League;
use App\Models\NBAPlayerInjury;
use App\Models\Player;
use App\Models\PlayerInjury;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\NFLInjury;
use Log, DB;

class PlayerInjuryUpdate extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:player-injury';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update player's injury";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $key      = env( "SPORTSAPP_KEY" );
        $password = env( "SPORTSAPP_PASSWORD" );

        $leagues = League::where( 'status', 1 )->get();
        foreach ( $leagues as $league ) {
            try {
                $api_settings = APISetting::where( 'league_id', $league->id )->firstOrFail();
                $api_end = $api_settings->players_injury_end_point;

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

                $players = $result->players ?? [];

                $table_name = $league->slug . '_player_injuries';

                //truncate whole table first
                DB::table($table_name)->truncate();

                foreach ( $players as $data ) {
                    $players_injury_table = new PlayerInjury();
                    $players_injury_table->setTable( $table_name );
                    $players_injury_table->updateOrCreate( [
                        "player_id" => $data->id
                    ], [
                            "player_firstName"                        => $data->firstName,
                            "player_lastName"                         => $data->lastName,
                            "player_primaryPosition"                  => $data->primaryPosition,
                            "player_jerseyNumber"                     => $data->jerseyNumber,
                            "player_currentTeam_id"                   => $data->currentTeam->id ?? null,
                            "player_currentTeam_abbreviation"         => $data->currentTeam->abbreviation ?? null,
                            "player_currentRosterStatus"              => $data->currentRosterStatus,
                            "player_currentInjury_description"        => $data->currentInjury->description ?? null,
                            "player_currentInjury_playingProbability" => $data->currentInjury->playingProbability ?? null,
                            'lastUpdatedOn'                           => Carbon::parse( $result->lastUpdatedOn )->subHour( 8 )
                        ]
                    );
                }
                Log::info( $league->name . " Player Injury updated " . count( $players ) );
            } catch ( \Exception $ex ) {
                Log::info( $ex->getMessage() );
            }
        }
    }
}
