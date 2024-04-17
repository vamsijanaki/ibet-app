<?php

namespace App\Http\Controllers\Admin;

use App\Models\{League, Stat};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatsController extends Controller {
    public function index( $league ) {

        $pageTitle = 'All Stats';
        $pageTitle .= " (" . strtoupper( $league ) . ")";

        $table_league = League::where( 'slug', $league )->first();

        $stats = Stat::searchable( [
            'key',
            'display_name'
        ] )->where( 'league_id', $table_league->id )
           ->with( 'league' )
           ->orderBy( 'key', 'asc' )
           ->paginate( getPaginate() );

        $leagues = League::latest()->get();

        return view( 'admin.stat', compact( 'pageTitle', 'stats', 'leagues', 'table_league' ) );
    }

    public function store( Request $request, $id = 0 ) {

        $this->validation( $request, $id );

       if ( $id ) {
            $stat         = Stat::findOrFail( $id );
            $notification = $request->league . ' Stat updated successfully';
        } else {
            $stat         = new Stat();
            $notification = $request->league . ' Stat added successfully';
        }

        $stat->league_id     = $request->league_id;
        $stat->key           = implode('|', $request->key);
        $stat->display_name  = $request->display_name;
        $stat->sort_order  = $request->sort_order ? : 0;
        $stat->market_ID  = $request->market_ID ? : '';

        $stat->save();

        $notify[] = [ 'success', $notification ];

        return back()->withNotify( $notify );
    }

    public function status( $id ) {
        return Stat::changeStatus( $id );
    }

    protected function validation( $request, $id ) {
        $request->validate( [
            'league_id'     => 'required|exists:leagues,id',
            'key'           => 'required|max:40',
            'display_name'  => 'required|max:40'
        ], [

        ] );
    }
}
