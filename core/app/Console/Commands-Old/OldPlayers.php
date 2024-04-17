<?php

namespace App\Console\Commands;

use App\Models\APISetting;
use App\Models\League;
use App\Models\Player;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class OldPlayers extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:old-players';

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

        $key      = env( "SPORTSAPP_KEY" );
        $password = env( "SPORTSAPP_PASSWORD" );

        $leagues = League::where( 'status', 1 )->get();
        foreach ( $leagues as $league ) {
            try {
                $api_settings = APISetting::where( 'league_id', $league->id )->firstOrFail();

                $api_end = $api_settings->players_end_point;

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

                $table_name = $league->slug . '_players';

                foreach ( $players as $data ) {
                    $players_table = new Player();
                    $players_table->setTable( $table_name );
                    $players_table->updateOrCreate( [
                        'player_id' => $data->player->id
                    ], [
                        'firstName'                => $data->player->firstName,
                        'lastName'                 => $data->player->lastName,
                        'primaryPosition'          => $data->player->primaryPosition,
                        'alternatePositions'       => $data->player->alternatePositions ? json_encode( $data->player->alternatePositions ) : null,
                        'jerseyNumber'             => $data->player->jerseyNumber,
                        'currentTeam_id'           => $data->player->currentTeam->id ?? null,
                        'currentTeam_abbreviation' => $data->player->currentTeam->abbreviation ?? null,
                        'currentRosterStatus'      => $data->player->currentRosterStatus,
                        'currentInjury'            => $data->player->currentInjury ? json_encode( $data->player->currentInjury ) : null,
                        'height'                   => $data->player->height,
                        'weight'                   => $data->player->weight,
                        'birthDate'                => $data->player->birthDate,
                        'age'                      => $data->player->age,
                        'birthCity'                => $data->player->birthCity,
                        'birthCountry'             => $data->player->birthCountry,
                        'rookie'                   => $data->player->rookie,
                        'highSchool'               => $data->player->highSchool,
                        'college'                  => $data->player->college,
                        'handedness'               => $data->player->handedness ? json_encode( $data->player->handedness ) : null,
//                    'officialImageSrc'         => $data->player->officialImageSrc,
                        'socialMediaAccounts'      => $data->player->socialMediaAccounts ? json_encode( $data->player->socialMediaAccounts ) : null,
                        'currentContractYear'      => $data->player->currentContractYear ? json_encode( $data->player->currentContractYear ) : null,
                        'drafted'                  => $data->player->drafted ? json_encode( $data->player->drafted ) : null,
                        'externalMappings'         => $data->player->externalMappings ? json_encode( $data->player->externalMappings ) : null,
                        'teamAsOfDate'             => $data->teamAsOfDate ? json_encode( $data->teamAsOfDate ) : null,
                        'lastUpdatedOn'            => Carbon::parse( $result->lastUpdatedOn )->subHour( 8 ),
                    ] );
                }
                Log::info( $league->name . " Player updated " . count( $players ) );
            } catch ( \Exception $ex ) {
                Log::info( $ex->getMessage() );
            }
        }
    }
}
