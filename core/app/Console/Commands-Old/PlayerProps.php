<?php

namespace App\Console\Commands;

use App\Models\APISetting;
use App\Models\GeneralSetting;
use App\Models\League;
use App\Models\PlayerProp;
use App\Models\PlayerStat;
use App\Models\ScheduleResult;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class PlayerProps extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:player-props';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update player props';

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
                $api_end      = $api_settings->players_props_end_point . '?api_key=' . $api_settings->api_key;

                $table_name       = $league->slug . '_schedule_results';
                $schedule_results = new ScheduleResult();
                $schedule_results->setTable( $table_name );
                $games = $schedule_results->where( 'league_id', $league->id )->get();

                foreach ( $games as $game ) {
                    $api_url = preg_replace( "/(\{matchSR\})/", $game->sr_id, $api_end );

                    $ch = curl_init();
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch, CURLOPT_URL, $api_url );
                    $resp = curl_exec( $ch );
                    curl_close( $ch );

                    $result = json_decode( $resp );

                    $generated_at              = $result->generated_at;
                    $sport_event_players_props = $result->sport_event_players_props ?? [];
                    $sport_event               = $sport_event_players_props->sport_event;
                    $players_props             = $sport_event_players_props->players_props;

                    $count = 0;
                    foreach ( $players_props as $player ) {
                        $table_name       = $league->slug . '_playerprops';
                        $playerprop_table = new PlayerProp();
                        $playerprop_table->setTable( $table_name );

                        $playerprop_table->updateOrCreate( [
                            "player_id"      => $player->player->id,
                            "sport_event_id" => $sport_event->id,
                            "league_id"      => $league->id
                        ], [
                            "sport_event_start_time"           => Carbon::parse( $sport_event->start_time )->setTimezone( 'America/Los_Angeles' ),
                            "sport_event_start_time_confirmed" => $sport_event->start_time_confirmed,
                            "sport_event_competitors"          => $sport_event->competitors ? json_encode( $sport_event->competitors ) : null,

                            "player_name"          => $player->player->name,
                            "player_competitor_id" => $player->player->competitor_id,
                            "player_markets"       => isset( $player->markets ) ? json_encode( $player->markets ) : null,

                            "players_markets_overall" => isset( $sport_event_players_props->players_markets ) ? json_encode( $sport_event_players_props->players_markets ) : null,

                            "generated_at" => Carbon::parse( $generated_at )->setTimezone( 'America/Los_Angeles' )
                        ] );
                        $count ++;
                    }
                    Log::info( $game->sr_id . " player Prop Totals = " . $count );
                }
            } catch ( \Exception $ex ) {
                Log::info( $ex->getMessage() );
                throw $ex;
            }
        }
    }
}
