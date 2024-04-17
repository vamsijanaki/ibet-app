<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Team extends Model {
    use Searchable;

    public function category() {
        return $this->belongsTo( Category::class );
    }

    public function league() {
        return $this->belongsTo( League::class );
    }

    public function games() {
        return $this->hasMany( Game::class );
    }

    public function teamImage($at_listing = false ) {
        
        if ( $at_listing ) {
            if ( file_exists(  getFilePath( 'team' ) . '/' . $this->image ) && is_file(  getFilePath( 'team' ) . '/' . $this->image ) ) {
                return asset( getFilePath( 'team' ) . '/' . $this->image );
            } else {
                return '';
            }
        }
        return getImage( getFilePath( 'team' ) . '/' . $this->image, getFileSize( 'team' ) );
       
    }
}
