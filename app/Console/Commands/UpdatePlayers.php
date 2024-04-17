<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\LeagueCallables;
use App\Models\League;
use Illuminate\Support\Facades\Log;


class UpdatePlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:players';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update player's for all teams in leagues";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leagues = League::where('status', 1)->get();
        foreach ($leagues as $league) {

            // Only nba
            if ($league->slug != 'tennis') {
             //  continue;
            }

            Log::info('Updating players for league: ' . $league->name . ' with ' . $league->api_provider);

            $provider = app(LeagueCallables::class, ['apiProvider' => $league->api_provider, 'this' => $this, 'league' => $league]);

            if (!$provider) {
                Log::error('Provider not found for league: ' . $league->name);
                continue;
            } 

            $provider->updatePlayers($league->slug, ['league' => $league, 'console' => $this]);

        }

    }
}
