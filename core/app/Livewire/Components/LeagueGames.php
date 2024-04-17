<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Game;
use App\Models\League;


class LeagueGames extends Component
{
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

    public $showBetSlip = false;

    public $sub_league = null;

    public function mount()
    {

        // Get all the leagues
        $this->leagues = League::where('status', '1')
            ->orderBy('sort_order', 'asc')
            ->get();

        // Always select the first league by default
        $this->filters['leagueId'] = $this->leagues->first()->id;

    }

    public function render()
    {

        // If is favorite, render the favorite games
        if ($this->filters['isFavorite']) {
            return view('livewire.user-favorites', [
                'favorites' => getFavoritesData(),
            ]);
        }

        // Apply Filters
        $games = $this->applyFilters();

        // Render the view
        return view('livewire.components.league-games', [
            'games' => $games,
        ]);

    }

    public function applyFilters()
    {
        // Get the games
        $games = Game::with(['league', 'stat', 'league.category', 'game_type'])
            ->where('league_id', $this->filters['leagueId'])
            ->where('status', 1)
            ->where(function ($q) {
                $q->where('bet_start_time', '<=', now())
                    ->where('bet_end_time', '>', now());
            });

        // Filter by game type
        if ($this->filters['gameType'] != 0 && $this->filters['subLeague']['id'] == null) {
              $games->where('game_type_id', $this->filters['gameType']);
        }

        // If sub league is selected
        if ($this->filters['subLeague']['id']) {
            $games->where('sub_league_id', $this->filters['subLeague']['id']);
        } else {
            $games->whereNull('sub_league_id');
        }

        // Filter by stat
        if ($this->filters['stat'] == 'trending') {

            // Get the top 10 players
            $uniquePlayers = $games->groupBy('player_one_id')
                ->orderBy('special_promotion')
                ->take(10)
                ->pluck('player_one_id')
                ->toArray();

            $games->whereIn('player_one_id', $uniquePlayers);
            // Order by start tim
            $games->orderBy('start_time', 'asc');

        } else {
            $stat = $this->filters['stat'];
            $games->whereHas('stat', function ($query) use ($stat) {
                $query->where('id', $stat);
            })->orderBy('start_time');
        }

        return $games->get();
    }

    // Listen for updateGames
    #[On('updateGames')]
    public function updateGames($params)
    {
        $this->filters = $params;

        // Set sub league
        if ($this->filters['subLeague']['id']) {
            $this->sub_league = $this->filters['subLeague']['id'];
        } else {
            $this->sub_league = null;
        }

        $this->dispatch('countdown_refresh');
    }


}
