<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\LeagueCallables;
use App\Models\League;
use Illuminate\Support\Facades\Log;


class updateScheduleResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:updateScheduleResults {--params=*}';

    /**
     * The console command descripyetion.
     *
     * @var string
     */
    protected $description = "Update schedule results";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $params = $this->option('params');
        $includeTennis = in_array('include_tennis', $params);


        $leagues = League::where('status', 1)->get();
        foreach ($leagues as $league) {
            
            // Skip tennis if include_tennis is not passed
            if ($league->slug == 'tennis' && !$includeTennis) {
                continue;
            }

            // Skip non-tennis leagues if include_tennis is passed
            if ($league->slug != 'tennis' && $includeTennis) {
                continue;
             }


           
            Log::info('Updating schedule results for league: ' . $league->name . ' with ' . $league->api_provider);

            $provider = app(LeagueCallables::class, ['apiProvider' => $league->api_provider, 'this' => $this, 'league' => $league]);

            if (!$provider) {
                Log::error('Provider not found for league: ' . $league->name);
                continue;
            } 

            $provider->updateScheduleResults();

        }

    }
}
