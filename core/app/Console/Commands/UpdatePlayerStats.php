<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\LeagueCallables;
use App\Models\League;
use Illuminate\Support\Facades\Log;


class UpdatePlayerStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:playerStats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update player's stats";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leagues = League::where('status', 1)->get();
        foreach ($leagues as $league) {

            // Run only if nba
            if ($league->slug != 'cfb') {
            //    continue;
            }

            Log::info('Updating player stats for league: ' . $league->name . ' with ' . $league->api_provider);

            $provider = app(LeagueCallables::class, ['apiProvider' => $league->api_provider, 'this' => $this, 'league' => $league]);

            if (!$provider) {
                Log::error('Provider not found for league: ' . $league->name);
                continue;
            } 

            $provider->updatePlayerStats($league->slug, ['league' => $league, 'console' => $this]);

        }

    }
}
