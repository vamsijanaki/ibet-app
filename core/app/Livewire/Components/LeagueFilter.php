<?php

namespace App\Livewire\Components;

use App\Models\League;
use Livewire\Component;
use App\Models\Stat;
use App\Models\Game;

use App\Livewire\Components\LeagueGames;

use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;


class LeagueFilter extends Component
{
    public $leagues;

    public $subleagues;

    public $filters = [
        'leagueId' => null,
        'subLeague' => [
            'id' => null,
            'league_id' => null,
        ],
        'gameType' => 2,
        'stat' => 'trending',
        'search' => '',
        'page' => 1,
        'perPage' => 8,
        'isFavorite' => false,
    ];

    protected $data = [
        'stats' => [],
        'games' => [],
        'has_rivals' => null,
    ];

    public function render()
    {

        return view('livewire.components.league-filter', [
            'stats' => $this->data['stats'],
            'has_rivals' => $this->data['has_rivals'],
        ]);

    }

    public function mount()
    {

        // Get all the leagues
        $this->leagues = League::where('status', '1')
            ->orderBy('sort_order', 'asc')
            ->get();

        $subLeaguesOrder = ['1Q', '2Q', '3Q', '4Q', '1H', '2H', 'SZN'];

        $this->subleagues = Game::where('status', 1)
            ->where('bet_start_time', '<=', now())
            ->where('bet_end_time', '>', now())
            ->whereNotNull('sub_league_id')
            ->whereNot('game_type_id', 5)
            ->select('sub_league_id', 'league_id')
            ->distinct()
            ->get()
            ->groupBy('league_id')
            ->map(function ($subleagues) use ($subLeaguesOrder) {
                return $subleagues->map(function ($subleague) {
                    return [
                        'id' => $subleague['sub_league_id'],
                        'league_id' => $subleague['league_id']
                    ];
                })->sortBy(function ($item) use ($subLeaguesOrder) {
                    return array_search($item['id'], $subLeaguesOrder);
                })->values()->toArray();
            })
            ->toArray();


        // Get all the rival games
        $this->data['has_rivals'] = Game::where('league_id', $this->leagues->first()->id)
            ->where('status', 1)
            ->where(function ($q) {
                $q->where('bet_start_time', '<=', now())
                    ->where('bet_end_time', '>', now());
            })->where('game_type_id', 5)
            ->count() > 0 ? true : false;

        $gameType = $this->filters['gameType'] ?? 2;

        // Get all stats
        $stats = Stat::with(['league', 'games'])
            ->whereHas('games', function ($q) use ($gameType) {
                $q->where('bet_start_time', '<=', now())
                    ->where('bet_end_time', '>', now())
                    ->where('game_type_id', $gameType)
                    ->whereNull('sub_league_id');
            })->where('league_id', $this->leagues->first()->id)
            ->orderBy('sort_order', 'ASC')
            ->get();

        // Always select the first league by default
        $this->filters['leagueId'] = $this->leagues->first()->id;

        // Set the stats
        $this->data['stats'] = $stats;

    }


    // Method to set single filter
    public function setFilter($key, $value)
    {
        $this->filters[$key] = $value;

        // If is favorite is set, set the game type to 5
        if ($key == 'isFavorite') {
            $this->filters['leagueId'] = null;
            // Emit an event to let anything update on frontend
            $this->dispatch('updateGames', $this->filters)->to(LeagueGames::class);
            return;
        } else {
            $this->filters['isFavorite'] = false;
        }

        // If leagueId is changed, reset the stats
        if ($key == 'leagueId') {
            $this->filters['stat'] = 'trending';

            // Check $value is 4:1Q, convert to array
            $subLeague = explode(':', $value);
            if (count($subLeague) > 1) {
                $this->filters['subLeague']['id'] = $subLeague[1];
                $this->filters['subLeague']['league_id'] = $value[0];
            } else {
                $this->filters['subLeague']['id'] = null;
                $this->filters['subLeague']['league_id'] = null;
            }

        }

        $this->filtersUpdated($key, $value);

        // Emit an event to let anything update on frontend
        $this->dispatch('updateGames', $this->filters)->to(LeagueGames::class);

        // Emit an browser event, let it know
        $this->dispatch('filterUpdated');
    }

    // Method to clear filters
    public function clearFilters()
    {
        $this->filters = [];

        // Emit an event to let anything update on frontend
        $this->dispatch('updateGames', $this->filters);
    }

    public function filtersUpdated($key, $value)
    {

        if ($key == 'leagueId') {
            $this->filters['gameType'] = 2;
        }

        // Get all the rival games
        $this->data['has_rivals'] = Game::where('league_id', $this->filters['leagueId'])
            ->where('status', 1)
            ->where(function ($q) {
                $q->where('bet_start_time', '<=', now())
                    ->where('bet_end_time', '>', now());
            })->where('game_type_id', 5)
            ->count() > 0 ? true : false;

        // Set default game type if not provided
        $gameType = $this->filters['gameType'] ?? 2;

        // Get stats with eager loading
        $stats = Stat::with(['league', 'games'])
            ->whereHas('games', function ($q) use ($gameType) {
                $q->where('bet_start_time', '<=', now())
                    ->where('bet_end_time', '>', now());
                if (!empty ($this->filters['subLeague']['id'])) {
                    $q->where('game_type_id', 2)
                        ->where('sub_league_id', $this->filters['subLeague']['id']);
                } else {
                    $q->where('game_type_id', $gameType);
                }
            });

        // If leagueId is set, then filter by league
        if (!empty($this->filters['subLeague']['id'])) {
            $stats->where('league_id', $this->filters['subLeague']['league_id']);
        } else {
            $stats->where('league_id', $this->filters['leagueId']);
        }

        // Fetch and assign stats
        $this->data['stats'] = $stats->orderBy('sort_order', 'ASC')->get();

        // If key is leagueId, reset the subLeague
        if ($key == 'leagueId') {
            // If sub league id is set, then set to first stat
            if ($this->filters['subLeague']['id']) {
                $this->filters['stat'] = $stats->first()->id ?? 'trending';
            } else {
                $this->filters['stat'] = 'trending';
            }

        }

        // If key is gameType, set first stat
        if ($key == 'gameType') {
            if ($value == 5) {
                $this->filters['stat'] = $stats->first()->id ?? 'trending';
            } else {
                $this->filters['stat'] = 'trending';
            }
        }

    }
}
