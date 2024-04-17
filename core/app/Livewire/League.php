<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Stat;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Livewire\Component;
use Session;
use Livewire\Attributes\On;

use App\Services\BetSlipService;

use Illuminate\Support\Facades\Log;

class League extends Component
{

    public $wire_league;

    public $leagues;

    public $game_type = 2;

    public $stat = 'trending';

    public $rivalMatch = false;

    public $games;

    public $slug;

    public $search;

    public $betSlipCart = [];

    public $showBetSlip;

    protected $queryString = ['search'];

    public $sub_league;

    public $isFavorite = false;

    public $h2hexists = false;

    public $playerIds = [];

    public $subLeaguesByLeague = [];


    #[On('game-added'), On('game-removed')]

    public function betSlipUpdate()
    {
        $this->betSlipCart = Session::get('betSlipCart');
    }

    #[On('toggle-betslip')]
    public function toggleBetSlip()
    {
        if ($this->showBetSlip) {
            $this->showBetSlip = false;
        } else {
            $this->showBetSlip = true;
        }
    }

    public function ClearSearch()
    {
        $this->search = '';
    }

    public function selectFavorite($val)
    {
        $this->isFavorite = true;
        $this->sub_league = null;
        $this->search = '';
        $this->stat = 'trending';
        $this->game_type = 2;

        $this->wire_league = \App\Models\League::where('status', '1')
        ->where('slug', 'nba')
        ->first();
        
    }

    public function filterLeague($slug, $is_fav = false)
    {
       // $this->isFavorite = $is_fav;
       //  if ($is_fav) {
       //     return ;
       // }

       $this->isFavorite = false;
        $this->sub_league = null;
        $this->search = '';
        $this->stat = 'trending';
        $this->game_type = 2;

        $this->wire_league = \App\Models\League::where('status', '1')
            ->where('slug', $slug)
            ->first();
        $this->rivalMatch = Game::where('league_id', $this->wire_league->id)
            ->where('status', 1)
            ->where(function ($q) {
                $q->where('bet_start_time', '<=', now())
                    ->where('bet_end_time', '>', now());
            })->where('game_type_id', 5)
            ->count();
    }

    public function filterStat($slug, $key)
    {
        $this->wire_league = \App\Models\League::where('status', '1')
            ->where('slug', $slug)
            ->first();
       // $this->search = '';
        $this->stat = $key;
    }


    public function setSessionTab($tab)
    {

        // Set the tab value in the session
        Session::put('current_tab', $tab);

    }


    public function filterSubLeague($sub_league)
    {
        $this->sub_league = $sub_league;
        $this->isFavorite = false;
        $this->game_type = 2;
        $this->search = '';

        $this->wire_league = \App\Models\League::where('status', '1')
            ->where('id', explode('_', $sub_league)[0])
            ->first();
       
    }

    public function selectGame($game_id, $type)
    {
        $betslip = new BetSlipService($this);

        $betslip->addBet($game_id, $type);

        $game = $this->betSlipCart;
        $schedule_game = Game::find($game_id);
        $already_selected_game = Game::whereIn('id', array_keys($game))
            ->where('player_one_id', $schedule_game->player_one_id)
           // ->whereNot('game_type_id', 5)
            ->first();

        // dev_log bet slip
       // $this->dispatch('dev_log', $this->betSlipCart);

        if ($already_selected_game) {

            if ($already_selected_game && $game_id != $already_selected_game->id ) {
                // 'Player already selected.
                $this->dispatch('show-toast', ['type' => 'info', 'message' => 'Player already selected.']);
                return false;
            }
        }

        if (count($game) >= 5 && !isset($this->betSlipCart[$game_id])) {
            $this->dispatch('show-toast', ['type' => 'info', 'message' => 'Maximum 5 bet.']);
            return false;
        }
        if (isset($this->betSlipCart[$game_id])) {
            $game[$game_id] = $type;
        } else {
            $game[$game_id] = $type;
        }

        Session::put('betSlipCart', $game);
        $this->betSlipCart = Session::get('betSlipCart');

        $this->showBetSlip = true;

        $this->dispatch('game-added')->to(BetSlip::class);
        $this->dispatch('game-added')->to(MobileBetSlipCount::class);
    }

    public function deSelectGame($game_id)
    {
        $game = $this->betSlipCart;
        unset($game[$game_id]);
        Session::put('betSlipCart', $game);
        $this->betSlipCart = Session::get('betSlipCart');

        $this->dispatch('game-removed')->to(BetSlip::class);
        $this->dispatch('game-removed')->to(MobileBetSlipCount::class);
    }

    public function selectH2h($game_id, $player_id)
    {
        $game = $this->betSlipCart;
        $schedule_game = Game::find($game_id);

        $this->dispatch('dev_log', $this->betSlipCart);

        if (count($game) >= 5 && !isset($this->betSlipCart[$game_id])) {
            $this->dispatch('show-toast', ['type' => 'info', 'message' => 'Maximum 5 bet.']);
            return false;
        }

        if (isset($this->betSlipCart[$game_id])) {
            if ($game[$game_id] == $player_id) {
                unset($game[$game_id]);
            } else {
                $game[$game_id] = $player_id;
            }
        } else {
            $game[$game_id] = $player_id;
        }

        Session::put('betSlipCart', $game);
        $this->betSlipCart = Session::get('betSlipCart');

        $this->showBetSlip = true;

        $this->dispatch('game-added')->to(BetSlip::class);
        $this->dispatch('game-added')->to(MobileBetSlipCount::class);
    }

    public function mount()
    {


        if (Session::get('current_tab') == 'favorites') {
            $this->isFavorite = true;
            Session::put('current_tab', '');
        }

        $this->leagues = \App\Models\League::where('status', '1')
            ->orderBy('sort_order', 'asc')
            ->get();

        $first_leagues = \App\Models\League::where('status', '1')
            ->orderBy('sort_order', 'asc')
            ->first();

        $this->wire_league = \App\Models\League::where('status', '1')
            ->where('slug', $first_leagues->slug)
            ->first();

        $this->rivalMatch = Game::where('league_id', $this->wire_league->id)
            ->where('status', 1)
            ->where(function ($q) {
                $q->where('bet_start_time', '<=', now())
                    ->where('bet_end_time', '>', now());
            })->where('game_type_id', 5)
            ->count();

            $subleagues = Game::where('status', 1)
            ->where(function ($q) {
                $q->where('bet_start_time', '<=', now())
                    ->where('bet_end_time', '>', now());
            })
            ->whereNotNull('sub_league_id')
            ->whereNot('game_type_id', 5)
            ->select('sub_league_id', 'league_id')
            ->distinct()
            ->get()
            ->groupBy('league_id')
            ->toArray();

            $subLeaguesOrder = ['1Q', '2Q', '3Q', '4Q', '1H', '2H', 'SZN'];

            foreach ($subleagues as $key => $value) {
            $subleagues[$key] = collect($value)->sortBy(function ($item) use ($subLeaguesOrder) {
                return array_search($item['sub_league_id'], $subLeaguesOrder);
            })->values()->toArray(); // Add ->values()->toArray() to re-index the array after sorting.
            }

            // Log
           // Log::info(print_r($subleagues, true));

            $this->subLeaguesByLeague = $subleagues;

        $this->betSlipCart = session()->has('betSlipCart') ? Session::get('betSlipCart') : [];

        $this->showBetSlip = (count($this->betSlipCart) > 0) ? true : false;

        $this->dispatch('countdown_refresh');
    }

    public function render()
    {

        $game_type = $this->game_type;
        

        $stats = Stat::with(['league', 'games'])
            ->whereHas('games', function ($q) use ($game_type) {
                $q->where('bet_start_time', '<=', now())
                    ->where('bet_end_time', '>', now())
                    ->where('game_type_id', $game_type);
                if (!empty($this->sub_league)) {
                    $s_id = explode('_', $this->sub_league);
                    if (is_array($s_id) && isset($s_id[1])) {
                        $q->where('sub_league_id', $s_id[1]);
                    }
                } else {
                    $q->whereNull('sub_league_id');
                }
            })->where('league_id', $this->wire_league->id)
            ->orderBy('sort_order', 'ASC')
            ->get();

            if (!empty($this->sub_league)) {
                $s_slug = explode('_', $this->sub_league);
                if (is_array($s_slug) && isset($s_slug[0])) {
                $this->wire_league = \App\Models\League::where('status', '1')
                    ->where('id', $s_slug[0])
                    ->first();
                }
            }

        $this->games = Game::with(['league', 'stat', 'league.category', 'game_type'])
            ->where('status', 1)
            ->where(function ($q) use ($game_type) {
                $q->where('bet_start_time', '<=', now())
                    ->where('bet_end_time', '>', now())
                    ->where('game_type_id', $game_type);
            })
            ->where('league_id', $this->wire_league->id);


        if (empty($this->sub_league)) {
            // Exclude games with sub_league_id = null
            $this->games->whereNull('sub_league_id');
            
        }


        if ($this->sub_league) {
            $s_id = explode('_', $this->sub_league);
            $this->games->where('league_id', $s_id[0]);
            $this->games->where('sub_league_id', $s_id[1]);
        }

        if ($this->search) {
            $this->stat = null;

            $player_table = $this->wire_league->slug . '_players';
            $this->games->leftJoin($player_table, $player_table . '.player_id', '=', 'games.player_one_id');
            $this->games->select([$player_table . '.*', 'games.*']);
            $this->games->where(function ($query) use ($player_table) {
                $query->where($player_table . '.full_name', 'like', '%' . $this->search . '%');
            });
        }

        $player_table = $this->wire_league->slug . '_players';

        if ($this->stat) {
            $this->search = '';
            $stat = $this->stat;

            if ($stat == 'trending') {

                $uniquePlayers = $this->games->groupBy('player_one_id')
                    ->orderBy('special_promotion')
                    ->take(10)
                    ->pluck('player_one_id')
                    ->toArray();

                $this->games->whereIn('player_one_id', $uniquePlayers);
                // Order by start tim
                $this->games->orderBy('start_time', 'asc');

            } else {
                $this->games->whereHas('stat', function ($query) use ($stat) {
                    $query->where('id', $stat);
                })->orderBy('start_time');

            }
        }


        //        \DB::enableQueryLog();
        $this->games = $this->games->get();

        $this->games->map(function ($q) {
            $to = $q->start_time;
            $q->time_diff = now()->diffInMinutes($to, false);
            //            dd([now()->format('d-m-Y-H-i-s'), $to->format('d-m-Y-H-i-s'), $q->time_diff]);
        });

        //        dd(\DB::getQueryLog());

        $this->dispatch('countdown_refresh');

        if ($this->isFavorite) {

            $this->sub_league = '';

            return view('livewire.favorites', [
                'stats' => $stats,
                'fav_data' => getFavoritesData(),
                'league_name' => $this->wire_league->name,
                'sub_leagues_by_league' => $this->subLeaguesByLeague,
            ]);

        }

        return view('livewire.league', [
            'stats' => $stats,
            'games' => $this->games,
            'sub_leagues_by_league' => $this->subLeaguesByLeague,
        ]);
    }
}
