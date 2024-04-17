<?php
// app/Contracts/LeagueCallables.php

namespace App\Contracts;

interface LeagueCallables {

    public function updatePlayers($params);

    public function updatePlayerInjuries($league, $params);

    public function updateScheduleResults($params);

    public function updatePlayerGameLogs($league, $params);

    public function updateDailyTeamLogs($league, $params);

    public function updatePlayerStats($league, $params);

    public function updatePlayerProps($league, $params);

    
}