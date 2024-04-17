<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\LeagueCallables;
use App\Models\League;
use Illuminate\Support\Facades\Log;
use App\Services\ApiTennisApiService;


class UpdateTennisEventsTournaments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:tennisEventsTournaments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update Tennis Events and Tournaments";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leagues = League::where('status', 1)->get();
        foreach ($leagues as $league) {

            // Run only if tennis
            if ($league->slug != 'tennis') {
                continue;
            }

            Log::info('Updating tennis events & schedules for league: ' . $league->name . ' with ' . $league->api_provider);

            $provider = new ApiTennisApiService($league, $this);

            if (!$provider) {
                Log::error('Provider not found for league: ' . $league->name);
                continue;
            } 

            $provider->updateEventsTournaments( ['league' => $league, 'console' => $this]);

        }

    }
}
