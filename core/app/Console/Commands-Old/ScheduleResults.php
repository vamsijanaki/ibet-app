<?php

namespace App\Console\Commands;

use App\Models\APISetting;
use App\Models\League;
use App\Models\NBAScheduleResult;
use App\Models\Player;
use App\Models\ScheduleResult;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class ScheduleResults extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:schedule-results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Schedule Results';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $teams = Team::latest()->get();

        $key      = env( "SPORTSAPP_KEY" );
        $password = env( "SPORTSAPP_PASSWORD" );

        $leagues = League::where( 'status', 1 )->get();
        foreach ( $leagues as $league ) {
            try {
                $api_settings = APISetting::where( 'league_id', $league->id )->firstOrFail();
                $api_end      = $api_settings->schedule_result_end_point;

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

                $result     = json_decode( $resp );
                $games      = $result->games ?? [];
                $references = $result->references ?? [];

                $table_name = $league->slug . '_schedule_results';

                foreach ( $games as $data ) {
                    $schedule_result = new ScheduleResult();
                    $schedule_result->setTable( $table_name );

                    $schedule = $data->schedule;
                    $score    = $data->score;
                    $schedule_result->updateOrCreate(
                        [ "schedule_id" => $schedule->id ],
                        [
                            "schedule_awayTeam_id"                 => $schedule->awayTeam->id,
                            "schedule_awayTeam_abbreviation"       => $schedule->awayTeam->abbreviation,
                            "schedule_startTime"                   => Carbon::parse( $schedule->startTime )->subHour( 8 ),
                            "schedule_endedTime"                   => Carbon::parse( $schedule->endedTime )->subHour( 8 ),
                            "schedule_homeTeam_id"                 => $schedule->homeTeam->id,
                            "schedule_homeTeam_abbreviation"       => $schedule->homeTeam->abbreviation,
                            "schedule_venue_id"                    => $schedule->venue->id,
                            "schedule_venue_name"                  => $schedule->venue->name,
                            "schedule_venueAllegiance"             => $schedule->venueAllegiance,
                            "schedule_scheduleStatus"              => $schedule->scheduleStatus,
                            "schedule_originalStartTime"           => Carbon::parse( $schedule->originalStartTime )->subHour( 8 ),
                            "schedule_delayedOrPostponedReason"    => $schedule->delayedOrPostponedReason,
                            "schedule_playedStatus"                => $schedule->playedStatus,
                            "schedule_attendance"                  => $schedule->attendance,
                            "schedule_officials"                   => $schedule->officials ? json_encode( $schedule->officials ) : null,
                            "schedule_broadcasters"                => $schedule->broadcasters ? json_encode( $schedule->broadcasters ) : null,
                            "schedule_weather"                     => $schedule->weather ? json_encode( $schedule->weather ) : null,
                            "score_currentQuarter"                 => $score->currentQuarter,
                            "score_currentQuarterSecondsRemaining" => $score->currentQuarterSecondsRemaining,
                            "score_currentIntermission"            => $score->currentIntermission,
                            "score_awayScoreTotal"                 => $score->awayScoreTotal,
                            "score_homeScoreTotal"                 => $score->homeScoreTotal,
                            "score_quarters"                       => $score->quarters ? json_encode( $score->quarters ) : null,

                            "lastUpdatedOn" => Carbon::parse( $result->lastUpdatedOn )->subHour( 8 )
                        ]
                    );
                }
                Log::info( $league->name . " Schedule results = " . count( $games ) );
            } catch ( \Exception $ex ) {
                Log::info( $ex->getMessage() );
            }
        }
    }
}
