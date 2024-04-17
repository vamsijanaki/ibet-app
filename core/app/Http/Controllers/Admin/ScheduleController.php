<?php

namespace App\Http\Controllers\Admin;

use App\Models\{Category, League, NBAScheduleResult, NFL2023scheduleresults, Schedule, ScheduleResult, Team};
use App\{Console\Commands\NFLScheduleResults, Rules\FileTypeValidate, Http\Controllers\Controller};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScheduleController extends Controller {
    public function index( $league ) {
        $pageTitle = 'All Schedules';
        $pageTitle .= " (" . strtoupper( $league ) . ")";

        $table_name = $league . '_schedule_results';

        $schedules = new ScheduleResult();
        $schedules->setTable( $table_name );

        $schedules = $schedules->searchable( [ 'home_name', 'away_name', 'venue_name' ] )
                               ->dateFilter( 'scheduled' )
                               ->orderBy( 'id', 'desc' )
                               ->paginate( getPaginate() );

        $table_league = League::where( 'slug', $league )->first();

        $teams = Team::where( 'league_id', $table_league->id )->pluck( 'short_name', 'team_id' );

        return view( 'admin.schedule.schedule', compact( 'pageTitle', 'league', 'schedules', 'teams' ) );
    }

    public function store( Request $request, $id = 0 ) {

        $this->validation( $request, $id );

        $table_league = League::where( 'slug', $request->league )->first();
        $table_name   = $request->league . '_schedule_results';

        if ( $id ) {
            $schedule = new ScheduleResult();
            $schedule->setTable( $table_name );
            $schedule     = $schedule->findOrFail( $id );
            $notification = $request->league . ' Schedule updated successfully';
        } else {
            $schedule = new ScheduleResult();
            $schedule->setTable( $table_name );
            $notification = $request->league . ' Schedule added successfully';

            $schedule->schedule_id = Str::orderedUuid();
        }

        $awayTeam = Team::where( 'team_id', $request->away_id )->first();
        $homeTeam = Team::where( 'team_id', $request->home_id )->first();

        $schedule->league_id  = $table_league->id;
        $schedule->away_id    = $awayTeam->team_id;
        $schedule->away_alias = $awayTeam->short_name;
        $schedule->home_id    = $homeTeam->team_id;
        $schedule->home_alias = $homeTeam->short_name;
        $schedule->venue_name = $request->venue_name;
        $schedule->scheduled  = $request->scheduled;

        $schedule->save();


        $notify[] = [ 'success', $notification ];

        return back()->withNotify( $notify );
    }

    protected function validation( $request, $id ) {
        $request->validate( [
            'away_id'    => 'required|exists:teams,team_id',
            'home_id'    => 'required|exists:teams,team_id',
            'venue_name' => 'nullable|max:40',
            'scheduled'  => 'required'
        ] );
    }
}
