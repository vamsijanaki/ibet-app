<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\Stat;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Livewire\Component;
use Session;
use Livewire\Attributes\On;

use Illuminate\Support\Facades\Log;

class League extends Component {

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

    protected $queryString = [ 'search' ];

    public $sub_league;

    public $isFavorite = false;

    public $h2hexists = false;


    #[On( 'game-added' ), On( 'game-removed' )]

    public function betSlipUpdate() {
        $this->betSlipCart = Session::get( 'betSlipCart' );
    }

    #[On( 'toggle-betslip' )]
    public function toggleBetSlip() {
        if ( $this->showBetSlip ) {
            $this->showBetSlip = false;
        } else {
            $this->showBetSlip = true;
        }
    }

    public function ClearSearch() {
        $this->search = '';
    }

    public function filterLeague( $slug, $is_fav = false ) {



        if ( $is_fav ) {
            $this->isFavorite = true;
        } else {
            $this->isFavorite = false;
            $this->wire_league = \App\Models\League::where( 'status', '1' )
            ->where( 'slug', $slug )
            ->first();
            $this->rivalMatch  = Game::where( 'league_id', $this->wire_league->id )
            ->where( 'status', 1 )
            ->where( function ( $q ) {
            $q->where( 'bet_start_time', '<=', now() )
                ->where( 'bet_end_time', '>', now() );
            } )->where( 'game_type_id', 5 )
            ->count();
            $this->search      = '';
            $this->stat        = 'trending';
            $this->game_type   = 2;
            $this->sub_league  = '';
        }

           
      
    }

    public function filterStat( $slug, $key ) {
        $this->wire_league = \App\Models\League::where( 'status', '1' )
                                               ->where( 'slug', $slug )
                                               ->first();
        $this->search      = '';
        $this->stat        = $key;
    }


    public function setSessionTab($tab) {

        // Set the tab value in the session
        Session::put('current_tab', $tab);

    }


    public function filterSubLeague( $sub_league ) {
        $this->sub_league = $sub_league;
    }

    public function selectGame( $game_id, $type ) {
        $game                  = $this->betSlipCart;
        $schedule_game         = Game::find( $game_id );
        $already_selected_game = Game::whereIn( 'id', array_keys( $game ) )
                                     ->where( 'player_one_id', $schedule_game->player_one_id )
                                     ->first();

                                                // dev_log bet slip
            $this->dispatch('dev_log', $this->betSlipCart);

        if ( $already_selected_game ) {
            if ( $game_id != $already_selected_game->id  && $this->betSlipCart[ $game_id ] != 'less' && $this->betSlipCart[ $game_id ] != 'more' ) {
               // session()->flash( 'message', 'Player already selected.' );
               // 'Player already selected.
                $this->dispatch('show-toast', ['type' => 'info', 'message' => 'Player already selected.']);

                return false;
            }
        }

        if ( count( $game ) >= 5  && !isset( $this->betSlipCart[ $game_id ] ) ) {
            //session()->flash( 'message', 'Maximum 5 bet.' );
            $this->dispatch('show-toast', ['type' => 'info', 'message' => 'Maximum 5 bet.']);
            return false;
        }
        if ( isset( $this->betSlipCart[ $game_id ] ) ) {
            $game[ $game_id ] = $type;
        } else {
            $game[ $game_id ] = $type;
        }

        Session::put( 'betSlipCart', $game );
        $this->betSlipCart = Session::get( 'betSlipCart' );

        $this->showBetSlip = true;

        $this->dispatch( 'game-added' )->to( BetSlip::class );
        $this->dispatch( 'game-added' )->to( MobileBetSlipCount::class );
    }

    public function deSelectGame( $game_id ) {
        $game = $this->betSlipCart;
        unset( $game[ $game_id ] );
        Session::put( 'betSlipCart', $game );
        $this->betSlipCart = Session::get( 'betSlipCart' );

        $this->dispatch( 'game-removed' )->to( BetSlip::class );
        $this->dispatch( 'game-removed' )->to( MobileBetSlipCount::class );
    }

    public function selectH2h( $game_id, $player_id ) {
        $game = $this->betSlipCart;

        $this->dispatch('dev_log', $this->betSlipCart);

        if ( count( $game ) >= 5  && !isset( $this->betSlipCart[ $game_id ] ) ) {

           // session()->flash( 'message', 'Maximum 5 bet.' );

            $this->dispatch('show-toast', ['type' => 'info', 'message' => 'Maximum 5 bet.']);

            return false;
        }

        if ( isset( $this->betSlipCart[ $game_id ] ) ) {
            if ( $game[ $game_id ] == $player_id ) {
                unset( $game[ $game_id ] );
            } else {
                $game[ $game_id ] = $player_id;
            }
        } else {
            $game[ $game_id ] = $player_id;
        }

        Session::put( 'betSlipCart', $game );
        $this->betSlipCart = Session::get( 'betSlipCart' );

        $this->showBetSlip = true;

        $this->dispatch( 'game-added' )->to( BetSlip::class );
        $this->dispatch( 'game-added' )->to( MobileBetSlipCount::class );
    }

    public function mount() {

        if (Session::get( 'current_tab' ) == 'favorites') {
            $this->isFavorite = true;
            Session::put('current_tab', '');
        } 

        $this->leagues     = \App\Models\League::where( 'status', '1' )
                                               ->orderBy( 'sort_order', 'asc' )
                                               ->get();
        $first_leagues     = \App\Models\League::where( 'status', '1' )
                                               ->orderBy( 'sort_order', 'asc' )
                                               ->first();
        $this->wire_league = \App\Models\League::where( 'status', '1' )
                                               ->where( 'slug', $first_leagues->slug )
                                               ->first();

        $this->rivalMatch = Game::where( 'league_id', $this->wire_league->id )
                                ->where( 'status', 1 )
                                ->where( function ( $q ) {
                                    $q->where( 'bet_start_time', '<=', now() )
                                      ->where( 'bet_end_time', '>', now() );
                                } )->where( 'game_type_id', 5 )
                                ->count();

        $this->betSlipCart = session()->has( 'betSlipCart' ) ? Session::get( 'betSlipCart' ) : [];

        $this->showBetSlip = ( count( $this->betSlipCart ) > 0 ) ? true : false;

        $this->dispatch( 'countdown_refresh' );
    }

    public function render() {

        $game_type = $this->game_type;

        $stats = Stat::with( [ 'league', 'games' ] )
                     ->whereHas( 'games', function ( $q ) use ( $game_type ) {
                         $q->where( 'bet_start_time', '<=', now() )
                           ->where( 'bet_end_time', '>', now() )
                           ->where( 'game_type_id', $game_type );
                           if ( $this->sub_league ) {
                            $q->where( 'sub_league_id', $this->sub_league );
                        } else {
                            $q->whereNull('sub_league_id');
                        }
                     } )->where( 'league_id', $this->wire_league->id )
                     ->orderBy( 'sort_order', 'ASC' )
                        ->get();

        $this->games = Game::with( [ 'league', 'stat', 'league.category', 'game_type' ] )
                           ->where( 'status', 1 )
                           ->where( function ( $q ) use ( $game_type ) {
                               $q->where( 'bet_start_time', '<=', now() )
                                 ->where( 'bet_end_time', '>', now() )
                                 ->where( 'game_type_id', $game_type );
                           } )
                           ->where( 'league_id', $this->wire_league->id );


                    // Add this line to get unique sub_league_ids
                    $uniqueSubLeagueIds = $this->games->pluck('sub_league_id')->unique()->toArray();
                    // Remove null values
                    $uniqueSubLeagueIds = array_filter($uniqueSubLeagueIds);
                    // Change sub leagues order 1Q, 2Q, 3Q, 4Q, 1H, 2H, SZN
                    $subLeaguesOrder = ['1Q', '2Q', '3Q', '4Q', '1H', '2H', 'SZN'];
                    usort($uniqueSubLeagueIds, function($a, $b) use ($subLeaguesOrder) {
                        return array_search($a, $subLeaguesOrder) - array_search($b, $subLeaguesOrder);
                    });

                    if ( empty($this->sub_league) ) {
                    // Exclude games with sub_league_id = null
                    $this->games->whereNull('sub_league_id');
                    }



        if ( $this->sub_league ) {
            $this->games->where( 'sub_league_id', $this->sub_league );
        } 

        if ( $this->search ) {
            $this->stat = null;

            $player_table = $this->wire_league->slug . '_players';
            $this->games->leftJoin( $player_table, $player_table . '.player_id', '=', 'games.player_one_id' );
            $this->games->select( [ $player_table . '.*', 'games.*' ] );
            $this->games->where( function ( $query ) use ( $player_table ) {
                $query->where( $player_table . '.full_name', 'like', '%' . $this->search . '%' );
            } );
        }

        $player_table = $this->wire_league->slug . '_players';
        
        if ( $this->stat ) {
            $this->search = '';
            $stat         = $this->stat;

            if ( $stat == 'trending' ) {
                
                $uniquePlayers = $this->games->groupBy('player_one_id')
                ->orderBy('special_promotion')
                ->take(10)
                ->pluck('player_one_id')
                ->toArray();
    
                $this->games->whereIn('player_one_id', $uniquePlayers);
                // Order by start tim
                $this->games->orderBy('start_time', 'asc');

            } else {
                $this->games->whereHas( 'stat', function ( $query ) use ( $stat ) {
                    $query->where( 'id', $stat );
                } )->orderBy( 'start_time' );

            }
        }




//        \DB::enableQueryLog();
        $this->games = $this->games->get();

        $this->games->map( function ( $q ) {
            $to           = $q->start_time;
            $q->time_diff = now()->diffInMinutes( $to, false );
//            dd([now()->format('d-m-Y-H-i-s'), $to->format('d-m-Y-H-i-s'), $q->time_diff]);
        } );

//        dd(\DB::getQueryLog());

        $this->dispatch( 'countdown_refresh' );

        if ( $this->isFavorite ) {
            return view( 'livewire.favorites', [
                'stats'    => $stats,
                'fav_data' => getFavoritesData(),
                'league_name' => $this->wire_league->name,
                'sub_leagues' => $uniqueSubLeagueIds
            ] );

        } 
       
        return view( 'livewire.league', [
            'stats' => $stats,
            'games' =>  $this->games,
            'sub_leagues' => $uniqueSubLeagueIds
        ] );
    }
}
