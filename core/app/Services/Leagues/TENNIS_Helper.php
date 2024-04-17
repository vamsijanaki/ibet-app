<?php

// File: app/Services/Leagues/TENNIS_Helper.php

// Kindly Don't remove this file, this is an tempalte used by all leagues to implement the methods in the LeagueCallables interface

namespace App\Services\Leagues;
use App\Models\ScheduleResult;
use App\Models\Player;
use App\Models\TennisEventsTournaments;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Log, DB;





class TENNIS_Helper
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
        if ($this->apiProvider == 'apitennis') {

            $players_table = $this->league->slug . '_players';

            $player = $data ?? [];

                $values = [
                    'league_id' => $this->league->id,
                    //'team_id' => $player['player_key'] ?? null,
                    'player_id' => $player['player_key'] ?? null,
                    'full_name' => $player['player_name'] ?? null,
                    'birth_place' => $player['player_country'] ?? null,
                    'birthdate' => $player['player_bday'] ?? null,
                    'primary_position' => 'TP',
                    'position' => 'TP',
                ];

                // Add updated_at and created_at
                $values['updated_at'] = Carbon::now();


                // Loop through values and remove null values
                foreach ($values as $key => $value) {
                    if (is_null($value)) {
                        //Log::info('Removing null value for key: ' . $key . ' in players for league: ' . $this->league->name);
                        unset($values[$key]);
                    }
                }

                if (isset($player['player_key'])) {
                    DB::table($players_table)->updateOrInsert(['player_id' => $player['player_key']], $values);
                }

        }

    }

    public function updateH2HPlayers ($data)
    {
        // Implement fetching data from the API for Fetching Players Data
        if ($this->apiProvider == 'apitennis') {

            $players_table = $this->league->slug . '_headtohead';

            $api_url = 'https://api.api-tennis.com/tennis/?method=get_H2H&first_player_key={firstPlayerKey}&second_player_key={secondPlayerKey}&APIkey=' . $data['api_key'];

            $h2h_groups = $data['h2h_groups'] ?? [];

            $count = 0;
            
            foreach ($h2h_groups as $h2h_group) {

                $firstPlayerKey = $h2h_group->home_id;
                $secondPlayerKey = $h2h_group->away_id;

                $url = str_replace('{firstPlayerKey}', $firstPlayerKey, $api_url);
                $url = str_replace('{secondPlayerKey}', $secondPlayerKey, $url);

                $response = Http::get($url)->json();

                $players = $response['result'] ?? [];

                $first_player_results = $players['firstPlayerResults'] ?? [];
                $second_player_results = $players['secondPlayerResults'] ?? [];

                foreach ([ $first_player_results, $second_player_results ] as $player_results) {
                    foreach ($player_results as $player_result) {

                        $values = [
                            'league_id' => $this->league->id,
                            'schedule_id' => $h2h_group->schedule_id ?? null,
                            'event_date' => $player_result['event_date'] ?? null,
                            'event_time' => $player_result['event_time'] ?? null,
                            'event_first_player' => $player_result['event_first_player'] ?? null,
                            'first_player_key' => $player_result['first_player_key'] ?? null,
                            'event_second_player' => $player_result['event_second_player'] ?? null,
                            'second_player_key' => $player_result['second_player_key'] ?? null,
                            'event_final_result' => $player_result['event_final_result'] ?? null,
                            'event_game_result' => $player_result['event_game_result'] ?? null,
                            'event_serve' => $player_result['event_serve'] ?? null,
                            'event_winner' => $player_result['event_winner'] ?? null,
                            'event_status' => $player_result['event_status'] ?? null,
                            'event_type_type' => $player_result['event_type_type'] ?? null,
                            'tournament_name' => $player_result['tournament_name'] ?? null,
                            'tournament_key' => $player_result['tournament_key'] ?? null,
                            'tournament_round' => $player_result['tournament_round'] ?? null,
                            'tournament_season' => $player_result['tournament_season'] ?? null,
                            'event_live' => $player_result['event_live'] ?? null,
                            'event_first_player_logo' => $player_result['event_first_player_logo'] ?? null,
                            'event_second_player_logo' => $player_result['event_second_player_logo'] ?? null,
                            'event_qualification' => $player_result['event_qualification'] ?? null,
                        ];

                        // Add updated_at and created_at
                        $values['updated_at'] = Carbon::now();
                        $values['created_at'] = Carbon::now();

                        // Loop through values and remove null values
                        foreach ($values as $key => $value) {
                            if (is_null($value)) {
                                //Log::info('Removing null value for key: ' . $key . ' in head to head for league: ' . $this->league->name);
                                unset($values[$key]);
                            }
                        }

                        DB::table($players_table)->updateOrInsert(
                            ['first_player_key' => $player_result['first_player_key'], 'second_player_key' => $player_result['second_player_key']],
                            $values
                        );

                        $count++;

                    }
                }
  

            }


        }

    }

    public function updateEventsTournaments($data)
    {

        if ($this->apiProvider == 'apitennis') {

            // Set Events Table
            $events_table = $this->league->slug . '_events';


            $event_model = new TennisEventsTournaments();

            $event_model->setTable($events_table);

            // Set Tournaments Table
            $tournaments_table = $this->league->slug . '_tournaments';

            $tournament_model = new TennisEventsTournaments();
            
            $tournament_model->setTable($tournaments_table);

            // Events from $data
            $events = $data['events'] ?? [];

            // Tournaments from $data
            $tournaments = $data['tournaments'] ?? [];

            // Update Events
            $count = 0;
            foreach ($events as $event) {

                $values = [
                    'event_type_key' => $event['event_type_key'] ?? null,
                    'event_type_type' => $event['event_type_type'] ?? null,
                ];

                // Add updated_at and created_at
                $values['updated_at'] = Carbon::now();
                $values['created_at'] = Carbon::now();

                // if updated_at is not set, set it to created_at
                if (!isset($values['updated_at'])) {
                    $values['created_at'] = Carbon::now();
                }

                // Loop through values and remove null values
                foreach ($values as $key => $value) {
                    if (is_null($value)) {
                        //Log::info('Removing null value for key: ' . $key . ' in events for league: ' . $this->league->name);
                        unset($values[$key]);
                    }
                }

                $event_model->updateOrCreate(
                    ['event_type_key' => $event['event_type_key']],
                    $values
                );

                $count++;

            }

            // Log count of events updated
            Log::info($this->league->name . " Events updated " . $count);

            // Update Tournaments
            $count = 0;
            foreach ($tournaments as $tournament) {

                $values = [
                    'tournament_key' => $tournament['tournament_key'] ?? null,
                    'tournament_name' => $tournament['tournament_name'] ?? null,
                    'event_type_key' => $tournament['event_type_key'] ?? null,
                    'event_type_type' => $tournament['event_type_type'] ?? null,
                    'tournament_surface' => $tournament['tournament_sourfaceevent_keyreference'] ?? null,
                ];

                
                // Add updated_at and created_at
                $values['updated_at'] = Carbon::now();

                // if updated_at is not set, set it to created_at
                if (!isset($values['updated_at'])) {
                    $values['created_at'] = Carbon::now();
                }

                // Loop through values and remove null values
                foreach ($values as $key => $value) {
                    if (is_null($value)) {
                        //Log::info('Removing null value for key: ' . $key . ' in tournaments for league: ' . $this->league->name);
                        unset($values[$key]);
                    }
                }

                $tournament_model->updateOrCreate(
                    ['tournament_key' => $tournament['tournament_key']],
                    $values
                );

                $count++;

        }

        // Log count of tournaments updated
        Log::info($this->league->name . " Tournaments updated " . $count);

        }

    }
    
    public function updateScheduleResults($data)
    {
        // Implement fetching data from the API for Fetching Schedule Results
        if ($this->apiProvider == 'apitennis') {

            Log::info('Updating schedule results for league: ' . $this->league->name . ' with ' . $this->apiProvider);

            $response = $data['response'] ?? [];
            $eventKey = $data['event_type_key'] ?? null;

            $fixtures = $response['result'] ?? [];

            $table_name = $this->league->slug . '_schedule_results';

            $schedule_result = new ScheduleResult();
            $schedule_result->setTable($table_name);

            $count = 0;
            
            foreach ($fixtures as $fixture) {

                $event_date = $fixture['event_date'] ?? null;
                $event_time = $fixture['event_time'] ?? null;

                // make the time go -9 hours
                $event_date_time = Carbon::parse($event_date . ' ' . $event_time)->subHours(9);

                $values = [
                    'league_id' => $this->league->id,
                    'schedule_id' => $fixture['event_key'] ?? null,
                    'event_type_key' => $eventKey ?? null,
                    'status' => $fixture['event_status'] ?? null,
                    'scheduled' => $event_date_time ?? null,
                    'sr_id' => $fixture['event_key'] ?? null,
                    'venue_name' => $fixture['tournament_name'] ?? null,
                    'season_year' => $fixture['tournament_season'] ?? null,
                    'home_id' => $fixture['first_player_key'] ?? null,
                    'away_id' => $fixture['second_player_key'] ?? null,
                    'home_name' => $fixture['event_first_player'] ?? null,
                    'away_name' => $fixture['event_second_player'] ?? null,
                    'home_alias' => $fixture['event_first_player'] ?? null,
                    'away_alias' => $fixture['event_second_player'] ?? null,
                ];

                
                // Add updated_at and created_at
                $values['updated_at'] = Carbon::now();
                
                // Loop through values and remove null values
                foreach ($values as $key => $value) {
                    if (is_null($value)) {
                        //Log::info('Removing null value for key: ' . $key . ' in schedule results for league: ' . $this->league->name);
                        unset($values[$key]);
                    }
                }

                // Update or Insert
                $schedule_result->updateOrCreate(
                    ['schedule_id' => $fixture['event_key']],
                    $values
                );

                $count++;

            }

            // Log count of schedule results updated
            Log::info($this->league->name . " Schedule Results updated " . $count);
       

     }

    }

    public function updatePlayerGameLogs($data)
    {
        // Implement fetching data from the API for Fetching Game Logs

    }

    public function updateDailyTeamLogs($data)
    {
        // Implement fetching data from the API for Fetching Team Logs

    }

    public function updatePlayerStats($data)
    {
        // Implement fetching data from the API for Fetching Player Stats

    }

    public function updatePlayerProps($data)
    {
        // Implement fetching data from the API for Fetching Player Props

    }
   
}