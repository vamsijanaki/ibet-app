<?php

// File: app/Services/Leagues/NFL_Helper.php

// Kindly Don't remove this file, this is an tempalte used by all leagues to implement the methods in the LeagueCallables interface

namespace App\Services\Leagues;

use App\Models\PlayerInjury;
use App\Models\ScheduleResult;
use App\Models\PlayerStat;
use App\Models\Player;
use App\Models\PlayerProp;
use App\Models\PlayerGameLog;
use App\Models\PlayerTeamLog;
use Carbon\Carbon;
use Log, DB;

class NFL_Helper
{
    public $league;
    public $console;
    public $instance;
    public $apiProvider;


    public function __construct($league, $console, $instance)
    {
        $this->league = $league;
        $this->console = $console;
        $this->instance = $instance;
        $this->apiProvider = $instance::$key;
    }

    public function updatePlayers($data)
    {
        // Implement fetching data from the API for Fetching Players Data
        if ($this->apiProvider == 'sportsradar') {

            // Implement fetching data from the API for Fetching Players Data
            $players = $data['res']['players'] ?? [];

            $table_name = $this->league->slug . '_players';

            $count = 0;
            foreach ($players as $player) {
                $players_table = new Player();
                $players_table->setTable($table_name);

                
                $primary_postition = $player['primary_position'] ?? null;

                // If it's a NA or null value, set it to the position
                if ($primary_postition == 'NA' || is_null($primary_postition)) {
                    $primary_postition = $player['position'];
                }

                $players_table->updateOrCreate([
                    'player_id' => $player['id'],
                    "league_id" => $this->league->id,
                    "team_id" => $data['team_id'],
                ], [
                    'status' => $player['status'],
                    'full_name' => $player['name'],
                    'first_name' => $player['first_name'] ?? null,
                    'last_name' => $player['last_name'] ?? null,
                    'abbr_name' => $player['abbr_name'] ?? null,
                    'height' => $player['height'] ?? null,
                    'weight' => $player['weight'] ?? null,
                    'position' => $player['position'] ?? null,
                    'primary_position' => $primary_postition,
                    'jersey_number' => $player['jersey_number'] ?? null,
                    'experience' => $player['experience'] ?? null,
                    'college' => $player['college'] ?? null,
                    'high_school' => $player['high_school'] ?? null,
                    'birth_place' => $player['birth_place'] ?? null,
                    'birthdate' => $player['birthdate'] ?? null,
                    'sr_id' => $player['sr_id'] ?? null,
                    'rookie_year' => $player['rookie_year'] ?? null,
                    'reference' => $player['reference'] ?? null,
                    'draft' => $player['draft'] ?? null,
                    'injuries' => $player['injuries'] ?? null,
                ]);
                $count++;
            }

            // Log the update count
            Log::info($this->league->name . " Player updated " . $count);

        }

    }

    public function updatePlayerInjuries($data)
    {

        if ($this->apiProvider == 'sportsradar') {

            $teams = $data['teams'] ?? [];
            $table_name = $this->league->slug . '_player_injuries';

            // Truncate the player injuries table
            DB::table($table_name)->truncate();

            $count = 0;
            foreach ($teams as $team) {
                $players = $team['players'];

                foreach ($players as $data) {
                    // Create or update player injury records
                    $playerInjury = [
                        "full_name" => $data['name'],
                        "position" => $data['position'],
                        "sr_id" => $data['sr_id'],
                        "injuries" => $data['injuries'] ?? null
                    ];

                    $this->updatePlayerInjury($table_name, $this->league->id, $data['id'], $playerInjury);
                    $count++;
                }
            }

            // Log the update count
            Log::info($this->league->name . " Player Injury updated " . $count);

        }
    }

    public function updateScheduleResults($data)
    {
        // Implement fetching data from the API for Fetching Schedule Results
        if ($this->apiProvider == 'sportsradar') {

            Log::info('Updating schedule results for league: ' . $this->league->name . ' with ' . $this->apiProvider);


            // Implement fetching data from the API for Fetching Schedule Results

            $weeks = $data['weeks'] ?? [];

            $table_name = $this->league->slug . '_schedule_results';

            // Loop through the games and update the schedule results
            $count = 0;

            foreach ($weeks as $week) {

                $games = $week['games'] ?? [];

                foreach ($games as $game) {

                    $schedule_result = new ScheduleResult();
                    $schedule_result->setTable($table_name);

                    // If scheduled is not empty and valid time
                    if (!empty($game['scheduled']) && strtotime($game['scheduled']) > 0) {
                        $game['scheduled'] = Carbon::parse($game['scheduled'])->setTimezone('America/Los_Angeles');
                    }

                    $schedule_result->updateOrCreate(

                        [
                            "schedule_id" => $game['id'],
                        ],
                        [
                            "league_id" => $this->league->id,
                            'season_id' => $data['id'] ?? null,
                            'season_type' => $data['type'] ?? 'REG',
                            'season_year' => $data['year'] ?? '2023',
                            'status' => $game['status'] ?? null,
                            'coverage' => $game['coverage'] ?? null,
                            'scheduled' => $game['scheduled'] ?? null,
                            'home_points' => $game['home_points'] ?? null,
                            'away_points' => $game['away_points'] ?? null,
                            'track_on_court' => $game['track_on_court'] ?? null,
                            'sr_id' => $game['sr_id'] ?? null,
                            'reference' => $game['reference'] ?? null,
                            'time_zones_venue' => $game['time_zones']['venue'] ?? null,
                            'time_zones_home' => $game['time_zones']['home'] ?? null,
                            'time_zones_away' => $game['time_zones']['away'] ?? null,
                            'venue_name' => $game['venue']['name'] ?? null,
                            'venue_capacity' => $game['venue']['capacity'] ?? null,
                            'venue_address' => $game['venue']['address'] ?? null,
                            'venue_city' => $game['venue']['city'] ?? null,
                            'venue_state' => $game['venue']['state'] ?? null,
                            'venue_zip' => $game['venue']['zip'] ?? null,
                            'venue_country' => $game['venue']['country'] ?? null,
                            'venue_location' => $game['venue']['location'] ?? null,
                            'broadcasts' => $game['broadcasts'] ?? null,
                            'home_id' => $game['home']['id'] ?? null,
                            'home_name' => $game['home']['name'] ?? null,
                            'home_alias' => $game['home']['alias'] ?? null,
                            'home_sr_id' => $game['home']['sr_id'] ?? null,
                            'home_reference' => $game['home']['reference'] ?? null,
                            'away_id' => $game['away']['id'] ?? null,
                            'away_name' => $game['away']['name'] ?? null,
                            'away_alias' => $game['away']['alias'] ?? null,
                            'away_sr_id' => $game['away']['sr_id'] ?? null,
                            'away_reference' => $game['away']['reference'] ?? null,
                        ]
                    );

                    $count++;


                }
            }


            // Log the update count
            Log::info($this->league->name . " Schedule Results updated " . $count);

        }
    }

    public function updatePlayerGameLogs($data)
    {
        // Implement fetching data from the API for Fetching Game Logs

        if ($this->apiProvider == 'sportsradar') {

            $this->updateDailyTeamLogs($data);

            Log::info('Updating player game logs for league: ' . $this->league->name . ' with ' . $this->apiProvider);

            // Implement fetching data from the API for Fetching Schedule Results
            $home_players = $data['res']['statistics']['home'] ?? [];
            $away_players = $data['res']['statistics']['away'] ?? [];
            $keys_to_skip = ['id', 'name', 'market', 'alias', 'sr_id', 'summary', 'touchdowns', 'interceptions', 'extra_points', 'first_downs', 'efficiency' ];

            // Remove keys to skip
            foreach ($home_players as $key => $value) {
                if (in_array($key, $keys_to_skip)) {
                    unset($home_players[$key]);
                }
            }

            // Remove keys to skip
            foreach ($away_players as $key => $value) {
                if (in_array($key, $keys_to_skip)) {
                    unset($away_players[$key]);
                }
            }

            $mergedStats = [];

                // Merge the home and away player stats
                foreach ($home_players as $stat => $statData) {

                    foreach ($statData['players'] as $player) {

                        // keys to skip
                        $keys_to_skip = ['id', 'name', 'jersey', 'position', 'sr_id'];

                        $s_stats = [];
                        foreach ($player as $key => $value) {
                            if (!in_array($key, $keys_to_skip)) {
                                $s_stats[$key] = $value;
                            }
                        }

                       // Add ids to mergedstats
                        $mergedStats[$player['id']]['id'] = $player['id'];
                        $mergedStats[$player['id']]['name'] = $player['name'];
                        $mergedStats[$player['id']]['sr_id'] = $player['sr_id'];
                        $mergedStats[$player['id']]['jersey'] = $player['jersey'];
                        $mergedStats[$player['id']]['position'] = $player['position'];
                        $mergedStats[$player['id']]['statistics'][$stat] = $s_stats;
                    }
                }

                // Merge the home and away player stats
                foreach ($away_players as $stat => $statData) {

                    foreach ($statData['players'] as $player) {

                        // keys to skip
                        $keys_to_skip = ['id', 'name', 'jersey', 'position', 'sr_id'];

                        $s_stats = [];
                        foreach ($player as $key => $value) {
                            if (!in_array($key, $keys_to_skip)) {
                                $s_stats[$key] = $value;
                            }
                        }

                        // Add ids to mergedstats
                        $mergedStats[$player['id']]['id'] = $player['id'];
                        $mergedStats[$player['id']]['name'] = $player['name'];
                        $mergedStats[$player['id']]['sr_id'] = $player['sr_id'];
                        $mergedStats[$player['id']]['jersey'] = $player['jersey'];
                        $mergedStats[$player['id']]['position'] = $player['position'];
                        $mergedStats[$player['id']]['statistics'][$stat] = $s_stats;
                    }
                }



          //      Log::info('Merged Stats: ' . print_r($mergedStats, true));


            // Set Table
            $table_name = $this->league->slug . '_daily_player_gamelogs';

            // Loop through the games and update the schedule results
            $count = 0;
            // Loop through the players and update the player stats
            foreach ($mergedStats as $id => $player) {

                $playerGameLog = new PlayerGameLog();
                $playerGameLog->setTable($table_name);

                $values = [
                    "league_id" => $this->league->id,
                    "game_id" => $data['res']['id'] ?? null,
                    "player_id" => $player['id'] ?? null,
                    "full_name" => $player['name'] ?? null,
                    "jersey_number" => $player['jersey'] ?? null,
                    "position" => $player['position'] ?? null,
                    "first_name" => $player['first_name'] ?? null,
                    "last_name" => $player['last_name'] ?? null,
                    "status" => $data['res']['status'] ?? null,
                    "coverage" => $data['res']['coverage'] ?? null,
                    "scheduled" => $data['res']['scheduled'] ?? null,
                    "lead_changes" => $data['res']['lead_changes'] ?? null,
                    "times_tied" => $data['res']['times_tied'] ?? null,
                    "clock" => $data['res']['clock'] ?? null,
                    "quarter" => $data['res']['quarter'] ?? null,
                    "track_on_court" => $data['res']['track_on_court'] ?? null,
                    "not_playing_reason" => $player['not_playing_reason'] ?? null,
                    "on_court" => $player['on_court'] ?? null,
                    "sr_id" => $player['sr_id'] ?? null,
                    "reference" => $player['reference'] ?? null,
                    "statistics" => isset($player['statistics']) ? json_encode($player['statistics']) : null,
                ];

                // Loop through values and remove null values
                foreach ($values as $key => $value) {
                    if (is_null($value)) {
                        unset($values[$key]);
                    }
                }

                $playerGameLog->updateOrCreate(
                    [
                        "player_id" => $player['id'],
                        "game_id" => $data['res']['id']
                    ],
                    $values
                );

                $count++;

            }

            // Log the update count
            Log::info($this->league->name . " Player Game Logs updated " . $count);


        }
    }

    public function updateDailyTeamLogs($data)
    {
        // Implement fetching data from the API for Fetching Team Logs

        if ($this->apiProvider == 'sportsradar') {

            Log::info('Updating team logs for league: ' . $this->league->name . ' with ' . $this->apiProvider);

            // If the data id is not set, return
            if (!isset($data['res']['id'])) {
                return;
            }

            // Implement fetching data from the API for Fetching Schedule Results
            $home_statistics = $data['res']['statistics']['home'] ?? [];
            $away_statistics = $data['res']['statistics']['away'] ?? [];
            $keys_to_skip = [ 'id', 'name', 'market', 'alias', 'sr_id' ];

            // Remove keys to skip
            foreach ($home_statistics as $key => $value) {
                if (in_array($key, $keys_to_skip)) {
                    unset($home_statistics[$key]);
                }
            }

            // Remove keys to skip
            foreach ($away_statistics as $key => $value) {
                if (in_array($key, $keys_to_skip)) {
                    unset($away_statistics[$key]);
                }
            }

            // Loop through all stats and remove players if exists 
            foreach ($home_statistics as $key => $stat) {
                if (array_key_exists('players', $stat)) {
                    unset($home_statistics[$key]['players']);
                }
            }

            // Loop through all stats and remove players if exists
            foreach ($away_statistics as $key => $stat) {
                if (array_key_exists('players', $stat)) {
                    unset($away_statistics[$key]['players']);
                }
            }

            // Set Table
            $table_name = $this->league->slug . '_daily_team_logs';

            $count = 0;

            foreach ($data['res']['statistics'] as $key => $team) {

                $teamLog = new PlayerTeamLog();
                $teamLog->setTable($table_name);

                // If key is home
                if ($key == 'home') {
                    $stats = $home_statistics;
                } else {
                    $stats = $away_statistics;
                }

                $values = [
                    "league_id" => $this->league->id,
                    "game_id" => $data['res']['id'] ?? null,
                    "team_id" => $team['id'] ?? null,
                    "status" => $data['res']['status'] ?? null,
                    "coverage" => $data['res']['coverage'] ?? null,
                    "scheduled" => $data['res']['scheduled'] ?? null,
                    "duration" => $data['res']['duration'] ?? null,
                    "attendance" => $data['res']['attendance'] ?? null,
                    "lead_changes" => $data['res']['lead_changes'] ?? null,
                    "times_tied" => $data['res']['times_tied'] ?? null,
                    "clock" => $data['res']['clock'] ?? null,
                    "quarter" => $data['res']['quarter'] ?? null,
                    "track_on_court" => $data['res']['track_on_court'] ?? null,
                    "entry_mode" => $data['res']['entry_mode'] ?? null,
                    "clock_decimal" => $data['res']['clock_decimal'] ?? null,
                    "name" => $team['name'] ?? null,
                    "alias" => $team['alias'] ?? null,
                    "sr_id" => $team['sr_id'] ?? null,
                    "reference" => $team['reference'] ?? null,
                    "market" => $team['market'] ?? null,
                    "points" => $data['res']['summary'][$key]['points'] ?? null,
                    "bonus" => $team['bonus'] ?? null,
                    "remaining_timeouts" => $team['remaining_timeouts'] ?? null,
                    "record_wins" => $data['res']['summary'][$key]['record']['wins'] ?? null,
                    "record_losses" => $data['res']['summary'][$key]['record']['losses'] ?? null,
                    "statistics" => json_encode($stats) ?? null,
                ];

                // Loop through values and remove null values
                foreach ($values as $key => $value) {
                    if (is_null($value)) {
                        unset($values[$key]);
                    }
                }

                $teamLog->updateOrCreate(
                    [
                        "team_id" => $team['id'],
                        "game_id" => $data['res']['id']
                    ],
                    $values
                );

            }
        }

    }

    public function updatePlayerStats($data)
    {
        // Implement fetching data from the API for Fetching Player Stats
        if ($this->apiProvider == 'sportsradar') {

            Log::info('Updating player stats for league: ' . $this->league->name . ' with ' . $this->apiProvider);

            $count = 0;

            // Set Table
            $table_name = $this->league->slug . '_playerstats';

            // Implement fetching data from the API for Fetching Players Data
            $players = $data['res']['players'] ?? [];

            $possible_stats = $data['res']['record'] ?? [];

            // Loop through the players and update the player stats
            foreach ($players as $player) {

                $playerStats = new PlayerStat();
                $playerStats->setTable($table_name);

                $values = [
                    "league_id" => $this->league->id,
                    "full_name" => $player['name'] ?? null,
                    "first_name" => $player['first_name'] ?? null,
                    "last_name" => $player['last_name'] ?? null,
                    "position" => $player['position'] ?? null,
                    "primary_position" => $player['primary_position'] ?? null,
                    "jersey_number" => $player['jersey_number'] ?? null,
                    "sr_id" => $player['sr_id'] ?? null,
                    'reference' => $player['reference'] ?? null,
                ];

                $statistics = [];

                // Check if possible stat keys are in Player and save to statistics
                foreach ($possible_stats as $key => $stat) {
                    if (array_key_exists($key, $player)) {
                        $statistics[$key] = $player[$key];
                    }
                }

                $values['statistics'] = json_encode($statistics);

                // Loop through values and remove null values
                foreach ($values as $key => $value) {
                    if (is_null($value)) {
                        //   Log::info('Removing null value for key: ' . $key . ' in schedule results for league: ' . $this->league->name);
                        unset($values[$key]);
                    }
                }

                $playerStats->updateOrCreate(
                    [
                        "player_id" => $player['id'],
                    ],
                    $values
                );

                $count++;

            }



            // Log the update count
            Log::info($this->league->name . " Player Stats updated " . $count);

        }
    }

    public function updatePlayerProps($data)
    {

        Log::info(print_r($data, true));

        // Implement fetching data from the API for Fetching Player Stats
        if ($this->apiProvider == 'sportsradar') {

            Log::info('Updating player props for league: ' . $this->league->name . ' with ' . $this->apiProvider);


            $table_name = $this->league->slug . '_playerprops';

            $generated_at = $data['res']['generated_at'] ?? null;
            $sport_event_players_props = $data['res']['sport_event_players_props'] ?? [];
            $sport_event = $sport_event_players_props['sport_event'];
            $players_props = $sport_event_players_props['players_props'];

            $count = 0;

            foreach ($players_props as $player) {
                $playerprop_table = new PlayerProp();
                $playerprop_table->setTable($table_name);

                $playerprop_table->updateOrCreate(
                    [
                        "player_id" => $player['player']['id'] ?? null,
                        "sport_event_id" => $sport_event['id'] ?? null,
                        "league_id" => $this->league->id
                    ],
                    [
                        "sport_event_start_time" => Carbon::parse($sport_event['start_time'])->setTimezone('America/Los_Angeles'),
                        "sport_event_start_time_confirmed" => $sport_event['start_time_confirmed'],
                        "sport_event_competitors" => $sport_event['competitors'] ? json_encode($sport_event['competitors']) : null,

                        "player_name" => $player['player']['name'],
                        "player_competitor_id" => $player['player']['competitor_id'],
                        "player_markets" => isset($player['markets']) ? json_encode($player['markets']) : null,

                        "players_markets_overall" => isset($sport_event_players_props['players_markets']) ? json_encode($sport_event_players_props['players_markets']) : null,

                        "generated_at" => Carbon::parse($generated_at)->setTimezone('America/Los_Angeles')
                    ]
                );
                $count++;
            }

            // Log the update count
            Log::info($this->league->name . " Player Prop updated " . $count);

        }
    }

    // Function to update or create player injury records
    private function updatePlayerInjury($table_name, $league_id, $player_id, $playerInjury)
    {
        $playerInjuryModel = new PlayerInjury();
        $playerInjuryModel->setTable($table_name);
        $playerInjuryModel->updateOrCreate(
            ["player_id" => $player_id, "league_id" => $league_id],
            $playerInjury
        );
    }

}