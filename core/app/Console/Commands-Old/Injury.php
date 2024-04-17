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

class Injury extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:injury';

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
        $leagues = League::where( 'status', 1 )->get();
        foreach ( $leagues as $league ) {
            if ( $league->slug != 'nba' ) {
                continue;
            }
            try {
                $api_settings = APISetting::where( 'league_id', $league->id )->firstOrFail();
                $api_url      = $api_settings->players_injury_end_point . '?api_key=' . $api_settings->api_key;
                if ( $api_settings->players_injury_end_point == '' ) {
                    continue;
                }

                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch, CURLOPT_URL, $api_url );
                $resp = curl_exec( $ch );
                curl_close( $ch );

                $result = json_decode( $resp );
                $teams  = $result->teams ?? [];

                $table_name = $league->slug . '_player_injuries';

                //truncate whole table first
                DB::table( $table_name )->truncate();

                $count = 0;
                foreach ( $teams as $team ) {
                    $players = $team->players;

                    foreach ( $players as $data ) {
                        $players_injury_table = new PlayerInjury();
                        $players_injury_table->setTable( $table_name );
                        $players_injury_table->updateOrCreate( [
                            "player_id" => $data->id,
                            "league_id" => $league->id
                        ], [
                                "full_name"        => $data->full_name,
                                "first_name"       => $data->first_name,
                                "last_name"        => $data->last_name,
                                "name_suffix"      => $data->name_suffix ?? '',
                                "position"         => $data->position,
                                "primary_position" => $data->primary_position,
                                "jersey_number"    => $data->jersey_number,
                                "sr_id"            => $data->sr_id,
                                "reference"        => $data->reference,
                                "injuries"         => $data->injuries ?? null
                            ]
                        );
                        $count ++;
                    }
                }
                Log::info( $league->name . " Player Injury updated " . $count );
            } catch ( \Exception $ex ) {
                Log::info( $ex->getMessage() );
                throw $ex;
            }
        }
    }
}
