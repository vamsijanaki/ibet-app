<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameType;
use App\Models\League;
use App\Models\NBAPlayer;
use App\Models\NBAScheduleResult;
use App\Models\NFL2023scheduleresults;
use App\Models\NFL2023WeeklyPlayerGameLogs;
use App\Models\NFLPlayers;
use App\Models\Player;
use App\Models\ScheduleResult;
use App\Models\Stat;
use App\Models\Team;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GameController extends Controller {
    protected $pageTitle;

    protected function gameData( $scope = null ) {

        if ( $scope ) {
            $games = Game::$scope();
        } else {
            $games = Game::query();
        }

        if ( request()->start_time ) {
            $games->DateTimeFilter( 'start_time' );
        }

        if ( request()->bet_start_time ) {
            $games->DateTimeFilter( 'bet_start_time' );
        }

        if ( request()->bet_end_time ) {
            $games->DateTimeFilter( 'bet_end_time' );
        }

        $games = $games->with( [
            'stat',
            'league.category',
            'game_type',
        ] )->filter( [
            'league_id',
            'player_one_id',
            'player_two_id'
        ] )
                       ->orderBy( 'id', 'desc' )
                       ->withCount( [ 'questions' ] )
                       ->paginate( getPaginate() );

        $pageTitle = $this->pageTitle;

        $leagues = League::whereHas( 'games' )->get();

        return view( 'admin.game.index', compact( 'pageTitle', 'games', 'leagues' ) );
    }

    public function index() {
        $this->pageTitle = 'All Games';

        return $this->gameData();
    }

    public function running() {
        $this->pageTitle = 'Running Games';

        return $this->gameData( 'running' );
    }

    public function upcoming() {
        $this->pageTitle = 'Upcoming Games';

        return $this->gameData( 'upcoming' );
    }

    public function Ended() {
        $this->pageTitle = 'Ended Games';

        return $this->gameData( 'expired' );
    }

    public function create() {
        $pageTitle  = 'Add New Game';
        $leagues    = League::with( 'category' )->orderBy( 'name' )->get();
        $game_types = GameType::orderBy( 'name' )->get();

        return view( 'admin.game.form', compact( 'pageTitle', 'leagues', 'game_types' ) );
    }

    public function teamsByCategory( $categoryId ) {
        $teams = Team::where( 'category_id', $categoryId )->orderBy( 'name' )->get();

        if ( count( $teams ) ) {
            return response()->json( [
                'teams' => $teams,
            ] );
        } else {
            return response()->json( [
                'error' => 'No teams found for this league\'s category',
            ] );
        }
    }

    public function schedulesByDate( Request $request ) {

        $league_id = $request->league_id;
        $league    = League::find( $league_id );
        $game_date = $request->game_date;

        $schedules = ( new ScheduleResult() )->setTable( $league->slug . '_schedule_results' )
                                             ->whereDate( 'scheduled', $game_date )
                                             ->get();

        $stats_identifier = Stat::select( 'id', 'display_name' )
                                ->where( 'league_id', $league_id )
                                ->where( 'status', 1 )
                                ->orderBy( 'sort_order', 'ASC' )
                                ->get();

        if ( count( $schedules ) ) {
            return response()->json( [
                'schedules'        => $schedules,
                'stats_identifier' => $stats_identifier
            ] );
        } else {
            return response()->json( [
                'error' => 'No schedules found for this date',
            ] );
        }
        
    }

    public function scheduleGameById( Request $request ) {
        $league_id = $request->league_id;
        $league    = League::find( $league_id );

        $schedule_id = $request->schedule_id;

        try {
            $schedule = ( new ScheduleResult() )->setTable( $league->slug . '_schedule_results' )
                                                ->where( 'schedule_id', $schedule_id )
                                                ->firstOrFail();

            $schedule->bet_start_time = Carbon::now()->setSeconds( 0 )->format( 'Y-m-d H:i:s' );
            $schedule->bet_end_time   = $schedule->scheduled->subMinutes( 3 )->format( 'Y-m-d H:i:s' );



            $status_key = 'ACT';

            // If league is MLB, status key is A
            if ( $league->slug == 'mlb' ) {
                $status_key = 'A';
            }

            // If league is tennis, get from player_id and no status looking
            if ( $league->slug == 'tennis' ) {
                $players_1 = ( new Player() )->setTable( $league->slug . '_players' )
                                           ->where( 'player_id', $schedule->away_id )
                                           ->orderBy( 'primary_position', 'ASC' )
                                           ->get();
            } else {
                $players_1 = ( new Player() )->setTable( $league->slug . '_players' )
                                         ->where( 'team_id', $schedule->away_id )
                                         ->where( 'status', $status_key )
                                         ->orderBy( 'primary_position', 'ASC' )
                                         ->get();
            }
          

            //todo:update nfl injury table and update these value
            $players_1->filter( function ( $q ) {
                $q->injury_description = @$q->getInjury()->desc;
                $q->playing_probablity = @$q->getInjury()->status;
                $q->injury_last_update = @showDateTime( $q->getInjury()->update_date, 'm/d/Y - g:i A' );
            } );

            // If league is tennis, get from player_id and no status looking
            if ( $league->slug == 'tennis' ) {
                $players_2 = ( new Player() )->setTable( $league->slug . '_players' )
                                           ->where( 'player_id', $schedule->home_id )
                                           ->orderBy( 'primary_position', 'ASC' )
                                           ->get();
            } else {
                $players_2 = ( new Player() )->setTable( $league->slug . '_players' )
                                         ->where( 'team_id', $schedule->home_id )
                                         ->where( 'status', $status_key )
                                         ->orderBy( 'primary_position', 'ASC' )
                                         ->get();
            }


            //todo:update nfl injury table and update these value
            $players_2->filter( function ( $q ) {
                $q->injury_description = @$q->getInjury()->desc;
                $q->playing_probablity = @$q->getInjury()->status;
                $q->injury_last_update = @showDateTime( $q->getInjury()->update_date, 'm/d/Y - g:i A' );
            } );

            return response()->json( [
                'schedule'  => $schedule,
                'players_1' => $players_1,
                'players_2' => $players_2
            ] );

            
        } catch ( \Exception $ex ) {
            return response()->json( [
                'error' => 'No game found for this schedule',
            ] );
        }
    }

    public function edit( $id ) {
        $game       = Game::with( 'stat' )->findOrFail( $id );
        $pageTitle  = "Update Game " . $game->game_type->name;
        $leagues    = League::latest()->with( 'category' )->get();
        $game_types = GameType::orderBy( 'name' )->get();

        if ( $game->game_type_id == 5 ) {
            return view( 'admin.game.form', compact( 'game', 'pageTitle', 'leagues', 'game_types' ) );
        } else if ( $game->game_type_id == 2 ) {
            return view( 'admin.game.form_std_edit', compact( 'game', 'pageTitle', 'leagues', 'game_types' ) );
        } else {
            return view( 'admin.game.form', compact( 'game', 'pageTitle', 'leagues', 'game_types' ) );
        }
    }

    public function store( Request $request, $id = 0 ) {
        $this->validation( $request, $id );
        $league = League::findOrFail( $request->league_id );

        if ( $id ) {
            $game         = Game::findOrFail( $id );
            $notification = 'Game updated successfully';
        } else {
            $game         = new Game();
            $notification = 'Game added successfully';
        }

        $stats = [ $request->stats ];

        $game->league_id     = $league->id;
        $game->game_type_id  = $request->game_type_id;
        $game->week          = $request->week;
        $game->game_date     = Carbon::parse( $request->game_date );
        $game->schedule_id   = $request->schedule_id;
        $game->player_one_id = $request->player_one_id;
        $game->player_two_id = $request->player_two_id;
//        $game->stats                   = $request->stats;
        $game->player_one_adjustment   = $request->player_one_adjustment;
        $game->player_two_adjustment   = $request->player_two_adjustment;
        $game->start_time              = Carbon::parse( $request->start_time );
        $game->bet_start_time          = Carbon::parse( $request->bet_start_time );
        $game->bet_end_time            = Carbon::parse( $request->bet_end_time );
        $game->slug                    = $request->slug;
        $game->special_promotion       = $request->special_promotion;
        $game->promo_player_adjustment = $request->promo_player_adjustment;

        // Allowed values for sub_league_id
        $allowed_sub_league_ids = [ '1Q', '2Q', '3Q', '4Q', '1H', '2H', 'SZN'];
        if ( in_array( $request->sub_league_id, $allowed_sub_league_ids ) ) {
            $game->sub_league_id = $request->sub_league_id;
        }

        if ( $request->hasFile( 'image' ) ) {
            $fileName           = fileUploader( $request->image, getFilePath( 'player' ), getFileSize( 'player' ) );
            $game->player_image = $fileName;
        }

        if ( $game->save() ) {
            $game->stat()->sync( $request->stats );
        }

        $notify[] = [ 'success', $notification ];

        if ( $id ) {
            return back()->withNotify( $notify );
        }

        return to_route( 'admin.game.running', $game->id )->withNotify( $notify );
    }

    public function updateStatus( $id ) {
        return Game::changeStatus( $id );
    }

    protected function validation( $request, $id ) {

        $request->validate( [
            'league_id'      => 'required|exists:leagues,id',
            'player_one_id'  => 'required',
            'slug'           => 'required|alpha_dash|max:255|unique:games,slug,' . $id,
            'start_time'     => 'required',
            'bet_start_time' => 'required',
            'bet_end_time'   => 'required|after:bet_start_time',
            'image'          => [ 'nullable', 'image', new FileTypeValidate( [ 'jpeg', 'jpg', 'png', 'webp' ] ) ],
        ], [
            'slug.alpha_dash'    => 'Only alpha numeric value. No space or special character is allowed',
            'bet_end_time.after' => 'Bet end time should be after the bet start time',
        ] );

    }

    public function createStd() {
        $pageTitle  = 'Add New Game';
        $leagues    = League::with( 'category' )->orderBy( 'name' )->get();
        $game_types = GameType::orderBy( 'name' )->get();

        return view( 'admin.game.form_std', compact( 'pageTitle', 'leagues', 'game_types' ) );
    }

    public function storeStd( Request $request ) {

        $request->validate( [
            'league_id'      => 'required|exists:leagues,id',
            'team_id'        => ['required_unless:league_id,14', 'exists:teams,team_id'],
            'player_id'      => 'required',
            'slug'           => 'required|alpha_dash|max:255|unique:games,slug',
            'start_time'     => 'required',
            'bet_start_time' => 'required',
            'bet_end_time'   => 'required|after:bet_start_time',
            'image'          => [ 'nullable', 'image', new FileTypeValidate( [ 'jpeg', 'jpg', 'png', 'webp' ] ) ],
        ], [
            'slug.alpha_dash'    => 'Only alpha numeric value. No space or special character is allowed',
            'bet_end_time.after' => 'Bet end time should be after the bet start time',
        ] );

        $league = League::findOrFail( $request->league_id );

        $notification = 'Game added successfully';

        $i = 0;
        foreach ( $request->player_adjustment as $player_adjustment ) {
            $slug = $request->slug . '-' . str_slug( implode( '-', $request->get( "stats_$i" ) ), '-' );

            $game                        = new Game();
            $game->league_id             = $league->id;
            $game->game_type_id          = $request->game_type_id;
            $game->week                  = $request->week;
            $game->game_date             = Carbon::parse( $request->game_date );
            $game->schedule_id           = $request->schedule_id;
            $game->team_one_id           = $request->team_id;
            $game->player_one_id         = $request->player_id;
            $game->player_one_adjustment = $player_adjustment;
//            $game->stats                 = $request->get( "stats_$i" );
            $game->start_time              = Carbon::parse( $request->start_time );
            $game->bet_start_time          = Carbon::parse( $request->bet_start_time );
            $game->bet_end_time            = Carbon::parse( $request->bet_end_time );
            $game->slug                    = $slug;
            $game->special_promotion       = $request->special_promotion;
            $game->promo_player_adjustment = $request->promo_player_adjustment;
            
            // Allowed values for sub_league_id
            $allowed_sub_league_ids = [ '1Q', '2Q', '3Q', '4Q', '1H', '2H', 'SZN'];
            if ( in_array( $request->sub_league_id, $allowed_sub_league_ids ) ) {
                $game->sub_league_id = $request->sub_league_id;
            }

            if ( $request->hasFile( 'image' ) ) {
                $fileName           = fileUploader( $request->image, getFilePath( 'player' ), getFileSize( 'player' ), @$game->player_image );
                $game->player_image = $fileName;
            }

            if ( $game->save() ) {
                $game->stat()->sync( $request->get( "stats_$i" ) );
            }
            $i ++;
        }

        $notify[] = [ 'success', $notification ];

        return to_route( 'admin.game.running', $game->id )->withNotify( $notify );
    }

    public function scheduleTeam( Request $request ) {
        $league_id   = $request->league_id;
        $league      = League::find( $league_id );
        $game_date   = Carbon::parse( $request->game_date )->format( 'Y-m-d' );
        $schedule_id = $request->schedule_id;
        try {
            $schedule = ( new ScheduleResult() )->setTable( $league->slug . '_schedule_results' )
                                                ->whereDate( 'scheduled', $game_date )
                                                ->where( 'schedule_id', $schedule_id )
                                                ->firstOrFail();

            $schedule->bet_start_time = Carbon::now()->setSeconds( 0 )->format( 'Y-m-d H:i:s' );
            $schedule->bet_end_time   = $schedule->scheduled->subMinutes( 3 )->format( 'Y-m-d H:i:s' );

            return response()->json( [
                'schedule' => $schedule,
                'teams'    => [
                    [
                        'id'   => $schedule->away_id,
                        'abbr' => $schedule->away_alias
                    ],
                    [
                        'id'   => $schedule->home_id,
                        'abbr' => $schedule->home_alias
                    ]
                ]
            ] );
        } catch ( \Exception $ex ) {
            return response()->json( [
                'error' => 'No game found for this schedule',
            ] );
        }
    }

    public function schedulePlayer( Request $request ) {
        $league_id = $request->league_id;
        $league    = League::find( $league_id );

        // If league is mlb, set status check by_vamsi
        $status_check = $league->slug == 'mlb' ? 'A' : 'ACT';

        try {
            $players = ( new Player() )->setTable( $league->slug . '_players' )
                                       ->where( 'team_id', $request->team_id )
                                       ->where( 'status', $status_check )
                                       ->orderBy( 'primary_position', 'ASC' )
                                       ->get();
            //todo:update nfl injury table and update these value
            $players->filter( function ( $q ) {
                $q->injury_description = @$q->getInjury()->desc;
                $q->playing_probablity = @$q->getInjury()->status;
                $q->injury_last_update = @showDateTime( $q->getInjury()->update_date, 'm/d/Y - g:i A' );
            } );

            return response()->json( [
                'players' => $players
            ] );
        } catch ( \Exception $ex ) {
            dd( $ex->getMessage() );

            return response()->json( [
                'error' => 'No player found for this team',
            ] );
        }
    }
}
