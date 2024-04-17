<?php

// File: app/Services/SportsRadarApiService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Contracts\LeagueCallables;
use App\Models\APISetting;
use App\Models\PlayerInjury;
use App\Models\ScheduleResult;
use App\Models\Team;
use Carbon\Carbon;
use Log, DB;


class SportsRadarApiService implements LeagueCallables
{

    public static $key = 'sportsradar';
    public static $name = 'SportsRadar API';

    public $helper;

    public $league;

    public $console;

    public $api_settings;

    public function __construct($league, $consoleInstance)
    {

        $this->league = $league;
        $this->console = $consoleInstance;

        // Check if helper is available for the league
        $helper = app('App\Services\Leagues\\' . $league->name . '_Helper', ['league' => $league, 'console' => $consoleInstance, 'instance' => $this]);
        if (!$helper) {
        //    Log::error('Helper not found for league: ' . $league->name);
            $consoleInstance->error('Helper not found for league: ' . $league->name);
            return;
        }

        $this->helper = $helper;

        try {
            // Fetch API settings for the league
            $api_settings = APISetting::where('league_id', $league->id)->firstOrFail();
            $this->api_settings = $api_settings;
        } catch (\Exception $e) {
            Log::error('API settings not found for league: ' . $league->name);
            $consoleInstance->error('API settings not found for league: ' . $league->name);
            return;
        }

    }

    public function updatePlayers($params = [])
    {
        // Implement fetching data from the API for Fetching Players Data
        try {

            $api_url = $this->api_settings->players_end_point . '?api_key=' . $this->api_settings->api_key;

            // Get Teams by league Id
            $teams = Team::where('league_id', $this->league->id)
                        ->when(in_array($this->league->slug, ['cfb', 'cbb']), function ($query) {
                            return $query->where('update_via_api', 'yes');
                        })
                        ->get();

            $count = 0;

            // Loop through teams and replace {team_id} in API url and fetch data
            foreach ($teams as $team) {

                // Add 2 seconds delay for each team
                sleep(2);

                $response = Http::get(preg_replace("/(\{teamid\})/", $team->team_id, $api_url));
                $data = $response->json();

             //   Log::info('Response for team: ' . $team->name . ' - ' . json_encode($data));


                // Call the helper function to update players
                $this->helper->updatePlayers(['team_id' => $team->team_id, 'res' => $data]);

                $count++;

                $players_count = count($data['players'] ?? []);

              //  Log::info('Players updated for team: ' . $team->name . ' - ' . $players_count . ' players updated');
            }


        } catch (\Exception $e) {
            Log::error('Error fetching player injuries for league: ' . $this->league->name . ' - ' . $e->getMessage());
            $this->console->error('Error fetching player injuries for league: ' . $this->league->name . ' - ' . $e->getMessage());
            return;
        }
    }

    public function updatePlayerInjuries($league, $params = [])
    {
        try {

            $api_url = $this->api_settings->players_injury_end_point . '?api_key=' . $this->api_settings->api_key;

            // Make HTTP request to fetch player injuries data
            $response = Http::get($api_url);

            // Decode API response
            $data = $response->json();

            // Call the helper function to update player injuries
            $this->helper->updatePlayerInjuries($data);

        } catch (\Exception $e) {
            Log::error('Error fetching player injuries for league: ' . $this->league->name . ' - ' . $e->getMessage());
            $this->console->error('Error fetching player injuries for league: ' . $this->league->name . ' - ' . $e->getMessage());
            return;
        }


    }

    public function updateScheduleResults($params = [])
    {

        try {

            $api_url = $this->api_settings->schedule_result_end_point . '?api_key=' . $this->api_settings->api_key;


            // Get api_variables
            $api_variables = json_decode($this->api_settings->api_variables, true);

            // Replace api_variables in the API url
            foreach ($api_variables as $variable) {
                $key = $variable['key'];
                $value = $variable['value'];
                $api_url = str_replace("{{$key}}", $value, $api_url);
            }

           // Log::info('Fetching schedule results for league: ' . $this->league->name . ' from ' . $api_url);

            // Make HTTP request to fetch schedule results data
            $response = Http::get($api_url);

            // Decode API response
            $data = $response->json();

            // Call the helper function to update schedule results
            if ($data) {
                $this->helper->updateScheduleResults($data);
            }

        } catch (\Exception $e) {
            Log::error('Error fetching schedule results for league: ' . $this->league->name . ' - ' . $e->getMessage());
            $this->console->error('Error fetching schedule results for league: ' . $this->league->name . ' - ' . $e->getMessage());
            return;
        }



    }

    public function updatePlayerGameLogs($league, $params)
    {
        // Implement fetching data from the API for Fetching Game Logs

        try {

            $api_url = $this->api_settings->players_game_log_end_point . '?api_key=' . $this->api_settings->api_key;

            // Get Schedule Results with status closed
            $table_name = $this->league->slug . '_schedule_results';
            $schedule_results = new ScheduleResult();
            $schedule_results->setTable($table_name);

            // Get last 7 days
            $from = Carbon::today()->subDays(8);

            // Get todays date
            $to = Carbon::today();

           // Get from Feb 1 2024
           // $from = Carbon::create(2024, 2, 1, 0, 0, 0);

            // To March 17 2024
           // $to = Carbon::create(2024, 3, 17, 0, 0, 0);

                // Include where date only if league is cfb and cbb
                if ($this->league->slug == 'cfb' || $this->league->slug == 'cbb') {
                    
                    date_default_timezone_set('America/Los_Angeles');

                    // Get 11/1/2023
                 //   $from = Carbon::create(2023, 11, 1, 0, 0, 0);

                    // To 1/21/2024
                   // $to = Carbon::create(2024, 1, 21, 0, 0, 0);

                    $teams_ids = DB::table('teams')
                    ->where('league_id', $this->league->id)
                    ->where('update_via_api', 'yes')
                    ->pluck('team_id')
                    ->toArray();

                    $schedules = $schedule_results
                        ->where('league_id', $this->league->id)
                        ->where('status', 'closed')
                        ->whereDate('scheduled', '>=', $from)
                        ->whereDate('scheduled', '<=', $to)
                        ->where(function ($query) use ($teams_ids) {
                            $query->whereIn('home_id', $teams_ids)
                                ->orWhereIn('away_id', $teams_ids);
                        })
                        ->get()
                        ->toArray();

                } else {
                    
                    Log ::info('From: ' . $from);
                    Log::info('To: ' . $to);

                    $schedules = $schedule_results
                        ->where('league_id', $this->league->id)
                        ->where('status', 'closed')
                        ->whereDate('scheduled', '>=', $from)
                        ->whereDate('scheduled', '<=', $to)
                        ->get()
                        ->toArray();
                }

                Log::info('Schedules: ' . count($schedules));


                $skipped_schedules = 0;

            // Loop through teams and replace {scheduleID} in API url and fetch data   
            foreach ($schedules as $schedule) {

                // sleep for 2 seconds
                sleep(2);

                $response = Http::get(preg_replace("/(\{scheduleID\})/", $schedule['schedule_id'], $api_url));
                $data = $response->json();


             //  Log::info('Response for schedule: ' . $schedule['schedule_id'] . ' - ' . json_encode($data));

                // Call the helper function to update players
                $this->helper->updatePlayerGameLogs(['schedule_id' => $schedule['schedule_id'], 'res' => $data]);

            }

            // Log skipped schedules
            Log::info('Skipped schedules: ' . $skipped_schedules);


        } catch (\Exception $e) {
            Log::error('Error fetching player game logs for league: ' . $this->league->name . ' - ' . $e->getMessage());
            $this->console->error('Error fetching player game logs for league: ' . $this->league->name . ' - ' . $e->getMessage());
            return;
        }

    }

    public function updateDailyTeamLogs($league, $params)
    {
        // Implement fetching data from the API for Fetching Team Logs

  

    }

    public function updatePlayerStats($league, $params)
    {
        // Implement fetching data from the API for Fetching Player Stats

        try {


            $api_url = $this->api_settings->players_stats_end_point . '?api_key=' . $this->api_settings->api_key;

            // Get api_variables
            $api_variables = json_decode($this->api_settings->api_variables, true);

            // Replace api_variables in the API url
            foreach ($api_variables as $variable) {
                $key = $variable['key'];
                $value = $variable['value'];
                $api_url = str_replace("{{$key}}", $value, $api_url);
            }

           // Log::info('Fetching player stats for league: ' . $this->league->name . ' from ' . $api_url);

            // Get Teams by league Id
            $teams = Team::where('league_id', $this->league->id)
                        ->when(in_array($this->league->slug, ['cfb', 'cbb']), function ($query) {
                            return $query->where('update_via_api', 'yes');
                        })
                        ->get();

            Log::info('Teams: ' . count($teams)) . 'for league: ' . $this->league->name;

            // Loop through teams and replace {team_id} in API url and fetch data
            foreach ($teams as $team) {

                // If league is cfb or cbb, skip team based on update_via_api field
                if ($this->league->slug == 'cfb' || $this->league->slug == 'cbb') {
                    if ($team->update_via_api == 'no') {
                        continue;
                    }
                }

                // Add 2 seconds delay for each team
                sleep(2);

                $response = Http::get(preg_replace("/(\{teamID\})/", $team->team_id, $api_url));
                $data = $response->json();

               // Log::info('Response for team: ' . $team->name . ' - ' . json_encode($data));


                // Call the helper function to update players
                $this->helper->updatePlayerStats(['team_id' => $team->team_id, 'res' => $data]);
            }

        } catch (\Exception $e) {
            Log::error('Error fetching player stats for league: ' . $this->league->name . ' - ' . $e->getMessage());
            $this->console->error('Error fetching player stats for league: ' . $this->league->name . ' - ' . $e->getMessage());
            return;
        }

    }

    public function updatePlayerProps($league, $params)
    {
        // Implement fetching data from the API for Fetching Player Props
        try {

            $api_url = $this->api_settings->players_props_end_point . '?api_key=' . $this->api_settings->api_key;

            // Get today, tomorrow, and day after tomorrow dates
            // Set timezone to Los Angeles
            date_default_timezone_set('America/Los_Angeles');

            // if league is NFL or CFB, get 7 days data
            if ($this->league->slug == 'nfl' || $this->league->slug == 'cfb') {
                $from = Carbon::today();
                $tomorrow = Carbon::tomorrow();
                $to = Carbon::tomorrow()->addDays(7);
            } else {
                $from = Carbon::today();
                $tomorrow = Carbon::tomorrow();
                $to = Carbon::tomorrow()->addDay();
            }

            // Log the dates
           // Log::info('Today: ' . $from);
          //  Log::info('Tomorrow: ' . $tomorrow);
         //   Log::info('Day after tomorrow: ' . $to);

            // Log to console
            $this->console->info('from: ' . $from);
            $this->console->info('Tomorrow: ' . $tomorrow);
            $this->console->info('to: ' . $to);

            $props_table = $this->league->slug . '_playerprops';

            $table_name = $this->league->slug . '_schedule_results';
            $schedule_results = new ScheduleResult();
            $schedule_results->setTable($table_name);

            $games = $schedule_results
                ->where('league_id', $this->league->id)
                ->whereDate('scheduled', '>=', $from)
                ->whereDate('scheduled', '<=', $to)
                ->whereNotNull('sr_id')
                ->get()
                ->toArray();

            // If games are more then 1, then empty the table
            if (count($games) > 1) {
                DB::table($props_table)->truncate();
            }

          //  Log::info('Games: ' . count($games));


            // Loop through teams and replace {team_id} in API url and fetch data
            foreach ($games as $game) {

            //    Log::info('Fetching player props for game: ' . $game['sr_id']);

                $response = Http::get(preg_replace("/(\{matchSR\})/", $game['sr_id'], $api_url));
                $data = $response->json();

             //   Log::info('Response for game: ' . $game['sr_id'] . ' - ' . json_encode($data));


                // Call the helper function to update players
                $this->helper->updatePlayerProps(['game_sr_id' => $game['sr_id'], 'res' => $data]);
            }

        } catch (\Exception $e) {
            Log::error('Error fetching player props for league: ' . $this->league->name . ' - ' . $e->getMessage());
            $this->console->error('Error fetching player props for league: ' . $this->league->name . ' - ' . $e->getMessage());
            return;
        }

    }

}
