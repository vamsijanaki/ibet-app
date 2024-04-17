<?php

namespace App\Http\Controllers\Admin;

use App\Models\{Category, League,};
use App\{
    Rules\FileTypeValidate,
    Http\Controllers\Controller,
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LeagueController extends Controller {
    public function index() {
        $pageTitle  = 'All Leagues';

        $leagues    = League::searchable( [
            'name',
            'slug',
            'category:name'
        ] )->with( 'category' )->orderBy( 'id', 'desc' )->paginate( getPaginate() );
        $categories = Category::orderBy( 'id', 'desc' )->get();

        // Added by vamsi
        $apiProviders = $this->getAPIProviders();

        return view( 'admin.league', compact( 'pageTitle', 'leagues', 'categories', 'apiProviders') );
    }

    private function getAPIProviders()
    {
        $providers = [];
        $servicePath = app_path('Services');
        $files = File::files($servicePath);
    
        foreach ($files as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            $class = 'App\Services\\' . $className;

             // Check if the class implements the LeagueCallables interface
            if (in_array('App\Contracts\LeagueCallables', class_implements($class))) {
                $providers[] = [
                    'name' => $class::$name,
                    'key' => $className
                ];
            }
              
        }
    
        return $providers;
    }

    public function store( Request $request, $id = 0 ) {

        $this->validation( $request, $id );

        if ( $id ) {
            $league       = League::findOrFail( $id );
            $notification = 'League updated successfully';
        } else {
            $league       = new League();
            $notification = 'League added successfully';
        }

        if ( $request->hasFile( 'icon' ) ) {
            $fileName      = fileUploader( $request->icon, getFilePath( 'icon' ), getFileSize( 'icon' ), @$league->icon );
            $league->icon = $fileName;
        }

        $league->category_id = $request->category_id;
        $league->name        = $request->name;
        $league->short_name  = $request->short_name;
        $league->slug        = $request->slug;
        $league->sort_order  = $request->sort_order ?? 0;
        $league->api_provider = $request->api_provider;

        if ($league->save()){
            create_table_for_league($league->slug);
        }

        $notify[] = [ 'success', $notification ];

        // An helper function to create API helpers for all leagues -- by_vamsi
        createAPIHelpers();

        return back()->withNotify( $notify );
    }

    public function status( $id ) {
        return League::changeStatus( $id );
    }

    protected function validation( $request, $id ) {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate( [
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|max:40',
            'short_name'  => 'required|max:40',
            'slug'        => 'required|alpha_dash|max:255|unique:leagues,slug,' . $id,
            'icon'       => [ $imageValidation, 'image', new FileTypeValidate( [ 'jpeg', 'jpg', 'png', 'svg' ] ) ],
        ], [
            'slug.alpha_dash' => 'Only alpha numeric value. No space or special character is allowed'
        ] );
    }
}
