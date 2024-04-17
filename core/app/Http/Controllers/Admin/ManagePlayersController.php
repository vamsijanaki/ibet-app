<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\FileManager;
use App\Models\League;
use App\Models\NBAPlayer;
use App\Models\NFLPlayers;
use App\Models\Player;
use App\Models\Team;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManagePlayersController extends Controller {
    public function index( $league ) {
        $pageTitle = "All Players";
        $pageTitle .= " (" . strtoupper( $league ) . ")";

        $table_name = $league . '_players';

        $players = new Player();
        $players->setTable( $table_name );

        $players = $players->searchable( [ 'full_name', 'first_name', 'last_name', 'jersey_number' ] )
                           ->with( [ 'team' ] )
                           ->orderBy( 'full_name', 'asc' )
                           ->paginate( getPaginate() );

        $table_league = League::where( 'slug', $league )->first();

        $teams = Team::where( 'league_id', $table_league->id )->pluck( 'short_name', 'team_id' );

        return view( 'admin.player', compact( 'pageTitle', 'players', 'league', 'teams' ) );
    }

    public function store( Request $request, $id = 0 ) {

        $this->validation( $request, $id );

        $table_name = $request->league . '_players';

        if ( $id ) {
            $player = new Player();
            $player->setTable( $table_name );
            $player       = $player->findOrFail( $id );
            $notification = $request->league . ' Player updated successfully';
        } else {
            $player       = new Player();
            $player       = $player->setTable( $table_name );
            $notification = $request->league . ' Player added successfully';

            // Auto-generate player_id for new entries
            $player->player_id = Str::orderedUuid(); // This line adds the auto-update logic for player_id
        }


        if ( $request->hasFile( 'image' ) ) {
            $fileName           = fileUploader( $request->image, getFilePath( 'player' ), getFileSize( 'player' ), @$player->image_path );
            $player->image_path = $fileName;
        }

        if ( $request->team_id ) {
            $team = Team::where( 'team_id', $request->team_id )->first();
        } else {
            $team = null;
        }

        $player->full_name        = $request->first_name . ' ' . $request->last_name;
        $player->first_name       = $request->first_name;
        $player->last_name        = $request->last_name;
        $player->primary_position = $request->primary_position;
        $player->team_id          = $team ? $team->team_id : null;
        $player->save();

        $notify[] = [ 'success', $notification ];

        return back()->withNotify( $notify );
    }

    protected function validation( $request, $id ) {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate( [
            'first_name'       => 'required|max:255',
            'last_name'        => 'required|max:255',
            'primary_position' => 'required',
            'team_id'          => 'required|exists:teams,team_id',
            'image'            => [
                $imageValidation,
                'image',
                new FileTypeValidate( [ 'jpeg', 'jpg', 'png', 'webp' ] )
            ],
        ], [

        ] );
    }
}
