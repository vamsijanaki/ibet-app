<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\LeagueCallables;


class UpdateDatafromAPIs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:playerInjuries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Loop should be implemented here.
        $provider = app(LeagueCallables::class, ['apiProvider' => 'sportsradar', 'this' => $this]);
        $data = $provider->fetchPlayerInjuries('nba', ['apiProvider' => 'sportsradar']);
        
    }
}
