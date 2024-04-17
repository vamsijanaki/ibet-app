<?php

// File: app/Services/Leagues/MLB_Helper.php

// Kindly Don't remove this file, this is an tempalte used by all leagues to implement the methods in the LeagueCallables interface

namespace App\Services\Leagues;

use App\Models\PlayerInjury;
use App\Models\ScheduleResult;
use App\Models\Player;
use App\Models\PlayerStat;
use App\Models\PlayerProp;
use App\Models\PlayerGameLog;
use App\Models\PlayerTeamLog;
use App\Models\APISetting;
use Carbon\Carbon;
use Log, DB;
use Illuminate\Support\Facades\Http;

class MLB_Helper
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
                $players_table->updateOrCreate([
                    'player_id' => $player['id'],
                    "league_id" => $this->league->id,
                    "team_id" => $data['team_id'],
                ], [
                    'status' => $player['status'],
                    'full_name' => $player['full_name'],
                    'first_name' => $player['first_name'] ?? null,
                    'last_name' => $player['last_name'] ?? null,
                    'abbr_name' => $player['abbr_name'] ?? null,
                    'height' => $player['height'] ?? null,
                    'weight' => $player['weight'] ?? null,
                    'position' => $player['position'] ?? null,
                    'primary_position' => $player['primary_position'] ?? null,
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
                    'updated' => Carbon::parse($player['updated'])->setTimezone('America/Los_Angeles'),
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
                        "full_name" => $data['full_name'] ?? null,
                        "first_name" => $data['first_name'] ?? null,
                        "last_name" => $data['last_name'] ?? null,
                        "name_suffix" => $data['name_suffix'] ?? null,
                        "position" => $data['position'] ?? null,
                        "primary_position" => $data['primary_position'] ?? null,
                        "jersey_number" => $data['jersey_number'] ?? null,
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

            $api_settings = APISetting::where('league_id', $this->league->id)->first();

            Log::info('Updating schedule results for league: ' . $this->league->name . ' with ' . $this->apiProvider);

            // Get External schedule events
            $externalSchedules = Http::get('https://api.sportradar.com/oddscomparison-player-props/trial/v2/en/sport_events/mappings.json?api_key=' . $api_settings->api_key);
            $externalSchedules = $externalSchedules->json();

            // Get External Teams
            $externalTeamEvents = Http::get('https://api.sportradar.com/oddscomparison-player-props/trial/v2/en/competitors/mappings.json?api_key=' . $api_settings->api_key);
            $externalTeamEvents = $externalTeamEvents->json();

            // Implement fetching data from the API for Fetching Schedule Results
            $games = $data['games'] ?? [];

            $table_name = $this->league->slug . '_schedule_results';

            // Loop through the games and update the schedule results
            $count = 0;
            foreach ($games as $game) {

                $schedule_result = new ScheduleResult();
                $schedule_result->setTable($table_name);

                
                // Get sr_id from $externalSchedules
                $sr_id = null;
                // Get home and away team sr_id from $externalTeamEvents
                $home_sr_id = null;
                $away_sr_id = null;

                // If mapping is not set, continue
                if (isset($externalSchedules['mappings'])) {

                    foreach ($externalSchedules['mappings'] as $externalSchedule) {
                        if ($externalSchedule['external_id'] == $game['id']) {
                            $sourceSRID = $externalSchedule['id'];
                            $sr_id = 'sr:match:' . explode(':', $sourceSRID)[2];
                            break;
                        }
                    }

                    foreach ($externalTeamEvents['mappings'] as $externalTeamEvent) {
                        if ($externalTeamEvent['external_id'] == $game['home']['id']) {
                            $sourceSRID = $externalTeamEvent['id'];
                            $home_sr_id = 'sr:team:' . explode(':', $sourceSRID)[2];
                        }
                        if ($externalTeamEvent['external_id'] == $game['away']['id']) {
                            $sourceSRID = $externalTeamEvent['id'];
                            $away_sr_id = 'sr:team:' . explode(':', $sourceSRID)[2];
                        }
                    }

                }

            

                $values = [
                    "league_id" => $this->league->id,
                    'season_id' => $data['season']['id'] ?? null,
                    'season_type' => $data['season']['type'] ?? null,
                    'season_year' => $data['season']['year'] ?? null,
                    'status' => $game['status'] ?? null,
                    'coverage' => $game['coverage'] ?? null,
                    'scheduled' => $game['scheduled'] ?? null,
                    'home_points' => $game['home_points'] ?? null,
                    'away_points' => $game['away_points'] ?? null,
                    'sr_id' => $sr_id ?? null,
                    'reference' => $game['reference'] ?? null,
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
                    'home_name' => ($game['home']['market'] ?? '') . ' ' . ($game['home']['name'] ?? ''),
                    'home_alias' => $game['home']['abbr'] ?? null,
                    'home_sr_id' => $home_sr_id ?? null,
                    'home_reference' => $game['home']['reference'] ?? null,
                    'away_id' => $game['away']['id'] ?? null,
                    'away_name' => ($game['away']['market'] ?? '') . ' ' . ($game['away']['name'] ?? ''),
                    'away_alias' => $game['away']['abbr'] ?? null,
                    'away_sr_id' => $away_sr_id ?? null,
                    'away_reference' => $game['away']['reference'] ?? null,
                ];

                // Loop through values and remove null values
                foreach ($values as $key => $value) {
                    if (is_null($value)) {
                        //   Log::info('Removing null value for key: ' . $key . ' in schedule results for league: ' . $this->league->name);
                        unset($values[$key]);
                    }
                }

                $schedule_result->updateOrCreate(
                    [
                        "schedule_id" => $game['id'],
                    ],
                    $values
                );

                $count++;


            }

            // Log the update count
            Log::info($this->league->name . " Schedule Results updated " . $count);

        }

    }

    public function updatePlayerGameLogs($data)
    {
        // Implement fetching data from the API for Fetching Game Logs

        if ($this->apiProvider == 'sportsradar') {

            Log::info('Updating player game logs for league: ' . $this->league->name . ' with ' . $this->apiProvider);

            // Log data
           // Log::info('Data: ' . json_encode($data));

            // Implement fetching data from the API for Fetching Schedule Results
            $home_players = $data['res']['game']['home']['players'] ?? [];
            $away_players = $data['res']['game']['away']['players'] ?? [];
            $players = array_merge($home_players, $away_players);

            // Log players count
            Log::info('Players count: ' . count($players));


            // Set Table
            $table_name = $this->league->slug . '_daily_player_gamelogs';

            // Loop through the games and update the schedule results
            $count = 0;
            // Loop through the players and update the player stats
            foreach ($players as $player) {

                $playerGameLog = new PlayerGameLog();
                $playerGameLog->setTable($table_name);

                $values = [
                    "league_id" => $this->league->id,
                    "game_id" => $data['res']['game']['id'] ?? null,
                    "player_id" => $player['id'] ?? null,
                    "full_name" => $player['full_name'] ?? null,
                    "first_name" => $player['first_name'] ?? null,
                    "last_name" => $player['last_name'] ?? null,
                    "status" => $data['res']['game']['status'] ?? null,
                    "coverage" => $data['res']['game']['coverage'] ?? null,
                    "scheduled" => $data['res']['game']['scheduled'] ?? null,
                    "lead_changes" => $data['res']['game']['lead_changes'] ?? null,
                    "times_tied" => $data['res']['game']['times_tied'] ?? null,
                    "clock" => $data['res']['game']['clock'] ?? null,
                    "quarter" => $data['res']['game']['quarter'] ?? null,
                    "track_on_court" => $data['res']['game']['track_on_court'] ?? null,
                    "not_playing_reason" => $player['not_playing_reason'] ?? null,
                    "on_court" => $player['on_court'] ?? null,
                    "position" => $player['position'] ?? null,
                    "primary_position" => $player['primary_position'] ?? null,
                    "sr_id" => $player['sr_id'] ?? null,
                    "reference" => $player['reference'] ?? null,
                    "statistics" => json_encode($player['statistics']) ?? null,
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
                        "game_id" => $data['res']['game']['id']
                    ],
                    $values
                );

            }


            // Update Team Logs
            $this->updateDailyTeamLogs($data);



        }
    }

    public function updateDailyTeamLogs($data)
    {
 // Implement fetching data from the API for Fetching Team Logs

 if ($this->apiProvider == 'sportsradar') {

    Log::info('Updating team logs for league: ' . $this->league->name . ' with ' . $this->apiProvider);

    // If the data id is not set, return
    if (!isset($data['res']['game']['id'])) {
        return;
    }


    // Implement fetching data from the API for Fetching Schedule Results
    $home_team = $data['res']['game']['home'] ?? [];
    $away_team = $data['res']['game']['away'] ?? [];

    // Set Table
    $table_name = $this->league->slug . '_daily_team_logs';

    // Loop through the games and update the schedule results
    $count = 0;
    // Loop through the players and update the player stats
    foreach ([$home_team, $away_team] as $team) {

        $teamLog = new PlayerTeamLog();
        $teamLog->setTable($table_name);

        $values = [
            "league_id" => $this->league->id,
            "game_id" => $data['res']['game']['id'] ?? null,
            "team_id" => $team['id'] ?? null,
            "status" => $data['res']['game']['status'] ?? null,
            "coverage" => $data['res']['game']['coverage'] ?? null,
            "scheduled" => $data['res']['game']['scheduled'] ?? null,
            "duration" => $data['res']['game']['duration'] ?? null,
            "attendance" => $data['res']['game']['attendance'] ?? null,
            "lead_changes" => $data['res']['game']['lead_changes'] ?? null,
            "times_tied" => $data['res']['game']['times_tied'] ?? null,
            "clock" => $data['res']['game']['clock'] ?? null,
            "quarter" => $data['res']['game']['quarter'] ?? null,
            "track_on_court" => $data['res']['game']['track_on_court'] ?? null,
            "entry_mode" => $data['res']['game']['entry_mode'] ?? null,
            "clock_decimal" => $data['res']['game']['clock_decimal'] ?? null,
            "name" => $team['name'] ?? null,
            "alias" => $team['alias'] ?? null,
            "sr_id" => $team['sr_id'] ?? null,
            "reference" => $team['reference'] ?? null,
            "market" => $team['market'] ?? null,
            "points" => $team['points'] ?? null,
            "bonus" => $team['bonus'] ?? null,
            "remaining_timeouts" => $team['remaining_timeouts'] ?? null,
            "record_wins" => $team['record']['wins'] ?? null,
            "record_losses" => $team['record']['losses'] ?? null,
            "scoring" => isset($team['scoring']) ? json_encode($team['scoring']) : null,
            "statistics" => isset($team['statistics']) ? json_encode($team['statistics']) : null,
            "coaches" => isset($team['coaches']) ? json_encode($team['coaches']) : null,
            "players" => isset($team['players']) ? json_encode($team['players']) : null,
            "officials" => isset($data['res']['game']['officials']) ? json_encode($data['res']['game']['officials']) : null,
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
                "game_id" => $data['res']['game']['id']
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

            // Loop through the players and update the player stats
            foreach ($players as $player) {

                $playerStats = new PlayerStat();
                $playerStats->setTable($table_name);

                $values = [
                    "league_id" => $this->league->id,
                    "full_name" => $player['full_name'] ?? null,
                    "first_name" => $player['first_name'] ?? null,
                    "last_name" => $player['last_name'] ?? null,
                    "position" => $player['position'] ?? null,
                    "primary_position" => $player['primary_position'] ?? null,
                    "jersey_number" => $player['jersey_number'] ?? null,
                    "sr_id" => $player['sr_id'] ?? null,
                    'reference' => $player['reference'] ?? null,
                    'total' => $player['statistics']['total'] ?? null,
                    'statistics' => json_encode($player['statistics'] ?? []),
                    'average' => $player['statistics']['average'] ?? null,
                ];

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

        $table_name = $this->league->slug . '_playerprops';

        // Implement fetching data from the API for Fetching Player Stats
        if ($this->apiProvider == 'sportsradar') {

            Log::info('Updating player props for league: ' . $this->league->name . ' with ' . $this->apiProvider);


            $generated_at = $data['res']['generated_at'] ?? null;
            $sport_event_players_props = $data['res']['sport_event_players_props'] ?? [];
            $sport_event = $sport_event_players_props['sport_event'] ?? [];
            $players_props = $sport_event_players_props['players_props'] ?? [];

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