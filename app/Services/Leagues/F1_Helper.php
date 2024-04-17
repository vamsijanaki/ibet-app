<?php

// File: app/Services/Leagues/F1_Helper.php

// Kindly Don't remove this file, this is an tempalte used by all leagues to implement the methods in the LeagueCallables interface

namespace App\Services\Leagues;
use Illuminate\Support\Facades\Http;
use App\Contracts\LeagueCallables;
use App\Models\APISetting;

use Log, DB;

class F1_Helper
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

    }

    public function updatePlayerInjuries($data)
    {

    }
    
    public function updateScheduleResults($data)
    {
        // Implement fetching data from the API for Fetching Schedule Results

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