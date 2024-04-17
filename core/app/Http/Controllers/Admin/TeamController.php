<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\League;
use App\Models\Team;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class TeamController extends Controller {
    public function index($league) {

        $pageTitle = 'All Teams';
        $pageTitle .= " (" . strtoupper( $league ) . ")";

        $table_league = League::where('slug', $league)->first();

        $teams     = Team::searchable( [
            'name',
            'slug',
            'category:name',
            'league:name'
        ] )->with( 'category', 'league' )
           ->where( 'league_id', $table_league->id )
           ->orderBy( 'id', 'desc' )
           ->paginate( getPaginate() );

        $categories = Category::latest()->get();

        $leagues    = League::latest()->get();

        return view( 'admin.team', compact( 'pageTitle', 'leagues', 'teams', 'categories', 'table_league' ) );

    }

    public function store( Request $request, $id = 0 ) {

        $this->validation( $request, $id );

        if ( $id ) {
            $team         = Team::findOrFail( $id );
            $notification = $request->league . ' Team updated successfully';
        } else {
            $team         = new Team();
            $notification = $request->league . ' Team added successfully';
        }
        if ( $request->hasFile( 'image' ) ) {
            $fileName    = fileUploader( $request->image, getFilePath( 'team' ), getFileSize( 'team' ), @$team->image );
            $team->image = $fileName;
        }

        $team->team_id     = $request->team_id;
        $team->category_id = $request->category_id;
        $team->name        = $request->name;
        $team->short_name  = $request->short_name;
        $team->slug        = $request->slug;
        $team->league_id   = $request->league_id;
        $team->city        = $request->city;

        // if $request->update_via_api is not set, set it to 'no' else yes
        $team->update_via_api = $request->update_via_api ? 'yes' : 'no';
        $team->save();

        $notify[] = [ 'success', $notification ];

        return back()->withNotify( $notify );
    }

    protected function validation( $request, $id ) {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate( [
            'category_id' => 'required|exists:categories,id',
            'league_id'   => 'required|exists:leagues,id',
            'name'        => 'required|max:255',
            'short_name'  => 'required|max:40',
            'slug'        => 'required|alpha_dash|max:255|unique:teams,slug,' . $id,
            'image'       => [ $imageValidation, 'image', new FileTypeValidate( [ 'jpeg', 'jpg', 'png', 'webp' ] ) ],
        ], [
            'slug.alpha_dash' => 'Only alpha numeric value. No space or special character is allowed',
        ] );
    }
}
