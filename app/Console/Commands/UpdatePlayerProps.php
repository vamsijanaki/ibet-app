<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\LeagueCallables;
use App\Models\League;
use Illuminate\Support\Facades\Log;


class UpdatePlayerProps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:playerProps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update player's props";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leagues = League::where('status', 1)->get();
        foreach ($leagues as $league) {

            $run_for = ['mlb', 'nba', 'nhl'];

            // skip
            if (in_array($league->slug, $run_for)) {
               continue;
            }

            // run only for nfl
            if ($league->slug != 'nba') {
             //   continue;
            }

            Log::info('Updating player props for league: ' . $league->name . ' with ' . $league->api_provider);

            $provider = app(LeagueCallables::class, ['apiProvider' => $league->api_provider, 'this' => $this, 'league' => $league]);

            if (!$provider) {
                Log::error('Provider not found for league: ' . $league->name);
                continue;
            } 

            $provider->updatePlayerProps($league->slug, ['league' => $league, 'console' => $this]);

        }

    }
}
