<?php

namespace App\Console\Commands;

use App\Models\APISetting;
use App\Models\League;
use App\Models\Player;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class Players extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:players';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update player's data";

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
                if ( $api_settings->players_end_point == '' ) {
                    continue;
                }
                $api_end = $api_settings->players_end_point . '?api_key=' . $api_settings->api_key;


                $teams = Team::where( 'league_id', $league->id )->get();

                $count = 0;
                foreach ( $teams as $team ) {
                    $api_url = preg_replace( "/(\{teamid\})/", $team->team_id, $api_end );

                    $ch = curl_init();
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch, CURLOPT_URL, $api_url );
                    $resp = curl_exec( $ch );
                    curl_close( $ch );

                    $result  = json_decode( $resp );
                    $players = $result->players ?? [];

                    $table_name = $league->slug . '_players';
                    foreach ( $players as $data ) {
                        $players_table = new Player();
                        $players_table->setTable( $table_name );
                        $players_table->updateOrCreate( [
                            'player_id' => $data->id,
                            "league_id" => $league->id,
                            "team_id"   => $team->team_id
                        ], [
                            'status'           => $data->status,
                            'full_name'        => $data->full_name,
                            'first_name'       => $data->first_name,
                            'last_name'        => $data->last_name,
                            'abbr_name'        => $data->abbr_name,
                            'height'           => $data->height,
                            'weight'           => $data->weight,
                            'position'         => $data->position,
                            'primary_position' => $data->primary_position,
                            'jersey_number'    => $data->jersey_number ?? null,
                            'experience'       => $data->experience,
                            'college'          => $data->college ?? null,
                            'high_school'      => $data->high_school ?? null,
                            'birth_place'      => $data->birth_place,
                            'birthdate'        => $data->birthdate,
                            'sr_id'            => $data->sr_id,
                            'rookie_year'      => $data->rookie_year ?? null,
                            'reference'        => $data->reference,
                            'draft'            => $data->draft ?? null,
                            'injuries'         => $data->injuries ?? null,
                            'updated'          => Carbon::parse( $data->updated )->setTimezone( 'America/Los_Angeles' ),
                        ] );
                        $count ++;
                    }
                }
                Log::info( $league->name . " Player updated " . $count );
            } catch ( \Exception $ex ) {
                Log::info( $ex->getMessage() );
                throw $ex;
            }
        }
    }
}
