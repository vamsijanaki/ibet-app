<?php

namespace App\Console;

use App\Console\Commands\updateScheduleResults;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $commands = [
        'App\Console\Commands\UpdatePlayerInjuries',
        'App\Console\Commands\updateScheduleResults',
        'App\Console\Commands\UpdatePlayers',
    ];

    protected function schedule(Schedule $schedule)
    {

       // Define Schedules for API update (by_vamsi)
        $schedule->command('import:playerInjuries')->hourlyAt(20)->timezone('America/Los_Angeles')->sendOutputTo(storage_path('logs/playerInjuries.log'));
        $schedule->command('import:updateScheduleResults')->hourlyAt(30)->timezone('America/Los_Angeles')->sendOutputTo(storage_path('logs/scheduleResults.log'));
        $schedule->command('import:players')->hourlyAt(45)->timezone('America/Los_Angeles')->sendOutputTo(storage_path('logs/players.log'));
        $schedule->command('import:UpdatePlayerGameAndTeamLogs')->hourlyAt(0)->timezone('America/Los_Angeles')->sendOutputTo(storage_path('logs/UpdatePlayerGameAndTeamLogs.log'));
        $schedule->command('import:playerStats')->daily()->timezone('America/Los_Angeles')->sendOutputTo(storage_path('logs/playerStats.log'));
        //$schedule->command('import:playerProps')->everyTenMinutes()->timezone('America/Los_Angeles')->sendOutputTo(storage_path('logs/playerProps.log'));


        // For Tennis

        //Weekly running event for TennisEvents & TennisTournaments
        $schedule->command('import:tennisEventsTournaments')->weekly()->timezone('America/Los_Angeles')->sendOutputTo(storage_path('logs/tennisEventsTournaments.log'));                            
        $schedule->command('import:updateScheduleResults --params=include_tennis')->everyThreeHours()->timezone('America/Los_Angeles')->sendOutputTo(storage_path('logs/scheduleResults.log'));

        //New API Call
        // $schedule->command('import:players')->daily();
        // $schedule->command('import:injury')->hourly();
        // $schedule->command('import:schedules')->hourly();
        //$schedule->command('import:game-logs')->hourly();
        // $schedule->command('import:statistics')->hourly();
        //$schedule->command('import:player-props')->hourly();




        //Old API Call
//        $schedule->command('import:players')->daily();
//        $schedule->command('import:player-injury')->hourly();
//        $schedule->command('import:schedule-results')->everySixHours();
//        $schedule->command('import:team-logs')->everySixHours();
//        $schedule->command('import:game-logs')->everySixHours();
//        $schedule->command('import:player-stat')->everySixHours();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
