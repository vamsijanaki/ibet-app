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

class Schedules extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:schedules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Schedules';

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
                $api_end      = $api_settings->schedule_result_end_point . '?api_key=' . $api_settings->api_key;
                if ( $api_settings->schedule_result_end_point == '' ) {
                    continue;
                }

                $api_url = preg_replace( "/(\{season\})/", $api_settings->season, $api_end );
                $api_url = preg_replace( "/(\{year\})/", $api_settings->year, $api_url );

                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch, CURLOPT_URL, $api_url );
                $resp = curl_exec( $ch );
                curl_close( $ch );

                $result = json_decode( $resp );

                $games = $result->games ?? [];

                $season_id   = $result->season->id;
                $season_type = $result->season->type;
                $season_year = $result->season->year;

                $table_name = $league->slug . '_schedule_results';
                $count      = 0;
                foreach ( $games as $data ) {
                    $schedule_result = new ScheduleResult();
                    $schedule_result->setTable( $table_name );

                    $schedule_result->updateOrCreate(
                        [
                            "schedule_id" => $data->id,
                            "league_id"   => $league->id
                        ], [
                            "season_id"        => $season_id,
                            "season_type"      => $season_type,
                            "season_year"      => $season_year,
                            "status"           => $data->status,
                            "coverage"         => $data->coverage,
                            "scheduled"        => Carbon::parse( $data->scheduled )->setTimezone( 'America/Los_Angeles' ),
                            "home_points"      => $data->home_points ?? null,
                            "away_points"      => $data->away_points ?? null,
                            "track_on_court"   => $data->track_on_court,
                            "sr_id"            => $data->sr_id ?? null,
                            "reference"        => $data->reference ?? null,
                            "time_zones_venue" => $data->time_zones->venue,
                            "time_zones_home"  => $data->time_zones->home ?? null,
                            "time_zones_away"  => $data->time_zones->away ?? null,
                            "venue_name"       => $data->venue->name,
                            "venue_capacity"   => $data->venue->capacity,
                            "venue_address"    => $data->venue->address,
                            "venue_city"       => $data->venue->city,
                            "venue_state"      => $data->venue->state ?? null,
                            "venue_zip"        => $data->venue->zip ?? null,
                            "venue_country"    => $data->venue->country,
                            "venue_location"   => $data->venue->location ?? null,

                            "broadcasts"     => $data->broadcasts ?? null,
                            "home_id"        => $data->home->id,
                            "home_name"      => $data->home->name,
                            "home_alias"     => $data->home->alias,
                            "home_sr_id"     => $data->home->sr_id ?? null,
                            "home_reference" => $data->home->reference,
                            "away_id"        => $data->away->id,
                            "away_name"      => $data->away->name,
                            "away_alias"     => $data->away->alias,
                            "away_sr_id"     => $data->away->sr_id ?? null,
                            "away_reference" => $data->away->reference,
                        ]
                    );
                    $count ++;
                }
                Log::info( $league->name . " Schedule results = " . $count );
            } catch ( \Exception $ex ) {
                Log::info( $ex->getMessage() );
                throw $ex;
            }
        }
    }
}
