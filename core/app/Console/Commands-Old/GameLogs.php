<?php

namespace App\Console\Commands;

use App\Models\APISetting;
use App\Models\League;
use App\Models\PlayerGameLog;
use App\Models\PlayerTeamLog;
use App\Models\ScheduleResult;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class GameLogs extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:game-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update player game logs';

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
                $api_end      = $api_settings->players_game_log_end_point . '?api_key=' . $api_settings->api_key;

                $table_name      = $league->slug . '_schedule_results';
                $schedule_result = new ScheduleResult();
                $schedule_result->setTable( $table_name );
                $games = $schedule_result->get();

                foreach ( $games as $game ) {
                    $api_url = preg_replace( "/(\{gameid\})/", $game->schedule_id, $api_end );

                    $ch = curl_init();
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch, CURLOPT_URL, $api_url );
                    $resp = curl_exec( $ch );
                    curl_close( $ch );

                    $result = json_decode( $resp );

                    //update home team game log
                    $home_team = $result->home ?? [];
                    $this->updateTeamGameLog( $league, $result, $home_team );

                    //update away team game log
                    $away_team = $result->away ?? [];
                    $this->updateTeamGameLog( $league, $result, $away_team );

                    //update home player game log
                    $home_players = $result->home->players ?? [];
                    $hcount       = 0;
                    foreach ( $home_players as $data ) {
                        $this->updatePlayerGameLog( $league, $result, $data );
                        $hcount ++;
                    }

                    //update away player game log
                    $away_players = $result->away->players ?? [];
                    $acount       = 0;
                    foreach ( $away_players as $data ) {
                        $this->updatePlayerGameLog( $league, $result, $data );
                        $acount ++;
                    }
                    $total = $hcount + $acount;
                    Log::info( $league->name . " Player log updated total " . $total );
                }
            } catch ( \Exception $ex ) {
                Log::info( $ex->getMessage() );
                throw $ex;
            }
        }
    }

    private function updateTeamGameLog( $league, $game, $team ) {
        $table_name    = $league->slug . '_daily_team_logs';
        $teamlog_table = new PlayerTeamLog();
        $teamlog_table->setTable( $table_name );

        $teamlog_table->updateOrCreate( [
            'game_id'   => $game->id,
            'team_id'   => $team->id,
            'league_id' => $league->id
        ], [
            'status'         => $game->status,
            'coverage'       => $game->coverage,
            'scheduled'      => Carbon::parse( $game->scheduled )->setTimezone( 'America/Los_Angeles' ),
            'duration'       => $game->duration ?? null,
            'attendance'     => $game->attendance,
            'lead_changes'   => $game->lead_changes,
            'times_tied'     => $game->times_tied,
            'clock'          => $game->clock,
            'quarter'        => $game->quarter,
            'track_on_court' => $game->track_on_court,
            'entry_mode'     => $game->entry_mode,
            'clock_decimal'  => $game->clock_decimal,

            "name"               => $team->name,
            "alias"              => $team->alias,
            "sr_id"              => $team->sr_id,
            "reference"          => $team->reference,
            "market"             => $team->market,
            "points"             => $team->points,
            "bonus"              => $team->bonus,
            "remaining_timeouts" => $team->remaining_timeouts,
            "record_wins"        => $team->record->wins,
            "record_losses"      => $team->record->losses,
            "scoring"            => $team->scoring ? json_encode( $team->scoring ) : null,
            "statistics"         => $team->statistics ? json_encode( $team->statistics ) : null,
            "coaches"            => $team->coaches ? json_encode( $team->coaches ) : null,
            "players"            => $team->players ? json_encode( $team->players ) : null,

            "officials" => $game->officials ? json_encode( $game->officials ) : null,
        ] );
    }

    private function updatePlayerGameLog( $league, $game, $data ) {
        $table_name      = $league->slug . '_daily_player_gamelogs';
        $playerlog_table = new PlayerGameLog();
        $playerlog_table->setTable( $table_name );
        $playerlog_table->updateOrCreate( [
            'game_id'   => $game->id,
            'player_id' => $data->id,
            'league_id' => $league->id
        ], [
            'full_name'        => $data->full_name,
            'jersey_number'    => $data->jersey_number,
            'first_name'       => $data->first_name,
            'last_name'        => $data->last_name,
            'position'         => $data->position,
            'primary_position' => $data->primary_position,
            'active'           => $data->active ?? null,

            'status'         => $game->status,
            'coverage'       => $game->coverage,
            'scheduled'      => Carbon::parse( $game->scheduled )->setTimezone( 'America/Los_Angeles' ),
            'lead_changes'   => $game->lead_changes,
            'times_tied'     => $game->times_tied,
            'clock'          => $game->clock,
            'quarter'        => $game->quarter,
            'track_on_court' => $game->track_on_court,

            'not_playing_reason' => $data->not_playing_reason ?? null,
            'on_court'           => $data->on_court,
            'sr_id'              => $data->sr_id,
            'reference'          => $data->reference,
            'statistics'         => ( $data->statistics ) ? json_encode( $data->statistics ) : null
        ] );
    }
}
