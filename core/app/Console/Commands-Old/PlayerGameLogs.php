<?php

namespace App\Console\Commands;

use App\Models\APISetting;
use App\Models\League;
use App\Models\PlayerGameLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class PlayerGameLogs extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:player-game-logs {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update player game logs by date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $date = $this->option( 'date' );

        $leagues = League::where( 'status', 1 )->get();

        $key      = env( "SPORTSAPP_KEY" );
        $password = env( "SPORTSAPP_PASSWORD" );

        foreach ( $leagues as $league ) {
            try {
                $api_settings = APISetting::where( 'league_id', $league->id )->firstOrFail();

                $api_end = $api_settings->players_game_log_end_point;
                $date    = $date ?: Carbon::now()->subDay( 1 )->format( 'Ymd' );

                $api_end = preg_replace( "/(\{day\})/", $date, $api_end );

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

                if($league->name == 'NBA'){

                }

                $gamelogs = $result->gamelogs ?? [];

                $table_name = $league->slug . '_daily_player_gamelogs';

                foreach ( $gamelogs as $data ) {
                    $playerlog_table = new PlayerGameLog();
                    $playerlog_table->setTable( $table_name );

                    $playerlog_table->updateOrCreate( [
                        'game_id'   => $data->game->id,
                        'player_id' => $data->player->id
                    ], [
                        'game_startTime'            => Carbon::parse( $data->game->startTime )->subHour( 8 ),
                        'game_awayTeamAbbreviation' => $data->game->awayTeamAbbreviation,
                        'game_homeTeamAbbreviation' => $data->game->homeTeamAbbreviation,
                        'player_firstName'          => $data->player->firstName,
                        'player_lastName'           => $data->player->lastName,
                        'player_position'           => $data->player->position,
                        'player_jerseyNumber'       => $data->player->jerseyNumber,
                        'team_id'                   => $data->team->id,
                        'team_abbreviation'         => $data->team->abbreviation,
                        'stats'                     => $data->stats ? json_encode( $data->stats ) : null,
                        'lastUpdatedOn'             => Carbon::parse( $result->lastUpdatedOn )->subHour( 8 )
                    ] );
                }
                Log::info( $league->name . " Player GameLog for " . Carbon::parse($date)->format('d M Y') . ' = ' . count( $gamelogs ) );
            } catch (\Exception $ex){
                Log::info( $ex->getMessage() );
            }
        }
    }
}
