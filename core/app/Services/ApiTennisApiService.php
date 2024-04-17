<?php

// File: app/Services/SportsRadarApiService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Contracts\LeagueCallables;
use App\Models\APISetting;
use App\Models\PlayerInjury;
use Carbon\Carbon;

use Log, DB;


class ApiTennisApiService implements LeagueCallables
{

    public static $key = 'apitennis';
    public static $name = 'Tennis API';

    public $helper;

    public $league;

    public $console;

    public $api_settings;

    public $api_url = 'https://api.api-tennis.com/tennis/';

    public function __construct($league, $consoleInstance)
    {

        $this->league = $league;
        $this->console = $consoleInstance;

        // Check if helper is available for the league
        $helper = app('App\Services\Leagues\\' . $league->name . '_Helper', ['league' => $league, 'console' => $consoleInstance, 'instance' => $this]);
        if (!$helper) {
            Log::error('Helper not found for league: ' . $league->name);
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


    public function updatePlayers($params)
    {
        // Implement fetching data from the API for Fetching Players Data

        $api_url = $this->api_settings->players_end_point . '&APIkey=' . $this->api_settings->api_key;

        // Get schedules from db
        $schedule_table = $this->league->slug . '_schedule_results';

        // Get schedules as array
        $schedules = DB::table($schedule_table)->select('schedule_id', 'home_id', 'away_id')->get()->toArray();

        $this->helper->updateH2HPlayers( [ 'h2h_groups' => $schedules, 'api_key' =>  $this->api_settings->api_key] );
        
        // Get first 2
       // $schedules = array_slice($schedules, 0, 2);

        // Batch API requests
        $playerIds = [];
        
        foreach ($schedules as $schedule) {
            $playerIds[] = $schedule->home_id;
            $playerIds[] = $schedule->away_id;
        }

        // Loop through schedules and get players
        $count = 0;
        foreach ($playerIds as $player) {

            // Get home player
            $player = Http::get(preg_replace('/(\{playerKey\})/', $player, $api_url))->json()['result'][0] ?? [];

               // Call the helper function to update players
                $this->helper->updatePlayers($player);

            $count++;
        }

     

        // Log Players
        Log::info('Players fetched for league: ' . $this->league->name . ' - ' . $count);




    }

    
    public function updateEventsTournaments($data)
    {

        // Implement fetching data from the API for Fetching Events and Tournaments
        try {

            $event_url = $this->api_url . '?method=get_events&APIkey=' . $this->api_settings->api_key;
            $tournaments = $this->api_url . '?method=get_tournaments&APIkey=' . $this->api_settings->api_key;


            $events_data = Http::get($event_url)->json()['result'] ?? [];
            $tournaments_data = Http::get($tournaments)->json()['result'] ?? [];

            Log ::info('Fetching events and tournaments for league: ' . $this->league->name . ' from ' . $event_url . ' and ' . $tournaments);

            // Call the helper function to update events and tournaments
                $this->helper->updateEventsTournaments(['events' => $events_data, 'tournaments' => $tournaments_data]);


        } catch (\Exception $e) {
            Log::error('Error fetching events and tournaments for league: ' . $this->league->name . ' - ' . $e->getMessage());
            $this->console->error('Error fetching events and tournaments for league: ' . $this->league->name . ' - ' . $e->getMessage());
            return;
        }

    }

    public function updatePlayerInjuries($league, $params = [])
    {

        
       
    }

    public function updateScheduleResults($params = [])
    {
         // Implement fetching data from the API for Fetching Schedule Results
         try {

            $api_url =  $this->api_settings->schedule_result_end_point . '&APIkey=' . $this->api_settings->api_key;

            Log::info('Fetching schedule results for league: ' . $this->league->name . ' from ' . $api_url);

            $event_keys = ['265', '266'];

            // Without time, get date
            // Set timezone
            date_default_timezone_set('America/Los_Angeles');
            $dateStart = date('Y-m-d');
            // 2 days after
            $dateStop = date('Y-m-d', strtotime('+2 days'));

            // Replace variables in url
            $api_url = preg_replace('/(\{dateStart\})/', $dateStart, $api_url);
            $api_url = preg_replace('/(\{dateStop\})/', $dateStop, $api_url);


            foreach($event_keys as $event_key) {

                // Replace event key in url
                $api_url_new = str_replace('{eventKey}', $event_key, $api_url);

                // Make HTTP request to fetch schedule results data
                $response = Http::get($api_url_new)->json();

                // Log
                Log::info('Fetching schedule results for event key: ' . $event_key . ' from ' . $api_url_new);

                // Call the helper function to update schedule results
                $this->helper->updateScheduleResults(['response' => $response, 'event_type_key' => $event_key]);
               
            }


        } catch (\Exception $e) {
            Log::error('Error fetching schedule results for league: ' . $this->league->name . ' - ' . $e->getMessage());
            $this->console->error('Error fetching schedule results for league: ' . $this->league->name . ' - ' . $e->getMessage());
            return;
        }

    }

    public function updatePlayerGameLogs($league, $params)
    {
       

    }

    public function updateDailyTeamLogs($league, $params)
    {
        // Implement fetching data from the API for Fetching Team Logs

    }

    public function updatePlayerStats($league, $params)
    {
        // Implement fetching data from the API for Fetching Player Stats

    }

    public function updatePlayerProps($league, $params)
    {
        // Implement fetching data from the API for Fetching Player Props

    }


}
