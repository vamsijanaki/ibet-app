<?php

namespace App\Models;

use App\Traits\BindsDynamically;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Game extends Model {
    use Searchable, GlobalStatus, BindsDynamically;

    protected $casts = [
        'stats'          => 'array',
        'game_date'      => 'date:Y-m-d',
        'start_time'     => 'datetime:Y-m-d H:i:s',
        'bet_start_time' => 'datetime:Y-m-d H:i:s',
        'bet_end_time'   => 'datetime:Y-m-d H:i:s'
    ];

    public function league() {
        return $this->belongsTo( League::class );
    }

    public function game_type() {
        return $this->belongsTo( GameType::class );
    }

    public function getStat( $stat ) {
        return Stat::where( 'key', $stat )->first();
    }

    public function questions() {
        return $this->hasMany( Question::class );
    }

    public function stat() {
        return $this->belongsToMany( Stat::class, 'game_stat_relation' );
    }

    public function teamOne() {
        return $this->belongsTo( Team::class, 'team_one_id', 'team_id' );
    }

    public function teamTwo() {
        return $this->belongsTo( Team::class, 'team_two_id', 'team_id' );
    }

    public function player_one() {
        $league = League::find( $this->league_id );
        if ( $league ) {
            $instance = ( new Player() )->setTable( $league->slug . '_players' );

            return $this->newBelongsTo( $instance->newQuery(), $this, 'player_one_id', 'player_id', 'player_one' );
        }
    }

    public function player_two() {
        $league = League::find( $this->league_id );
        if ( $league ) {
            $instance = ( new Player() )->setTable( $league->slug . '_players' );

            return $this->newBelongsTo( $instance->newQuery(), $this, 'player_two_id', 'player_id', 'player_two' );

        }
    }

    public function getOpponentTeam() {
        if ( $this->team_one_id == $this->schedule->schedule_homeTeam_id ) {
            return $this->schedule->schedule_awayTeam_abbreviation;
        } else {
            return $this->schedule->schedule_homeTeam_abbreviation;
        }
    }

    public function schedule() {
        $league = League::find( $this->league_id );
        if ( $league ) {
            $instance = ( new ScheduleResult() )->setTable( $league->slug . '_schedule_results' );

            return $this->newHasOne( $instance->newQuery(), $this, 'schedule_id', 'schedule_id', 'schedule' );
        }
    }

    // Scopes
    public function scopeRunning( $query ) {
        return $query->where( 'bet_start_time', '<=', now() )->where( 'bet_end_time', '>=', now() );
    }

    public function scopeUpcoming( $query ) {
        return $query->where( 'bet_start_time', '>=', now() );
    }

    public function scopeExpired( $query ) {
        return $query->where( 'bet_end_time', '<', now() );
    }

    public function getIsUpcomingAttribute() {
        return now()->lt( $this->bet_start_time );
    }

    public function getIsRunningAttribute() {
        return now()->gte( $this->bet_start_time ) && now()->lte( $this->bet_end_time );
    }

    public function getIsExpiredAttribute() {
        return now()->gt( $this->bet_end_time );
    }

    public function scopeHasActiveCategory( $query ) {
        return $query->whereHas( 'league.category', function ( $category ) {
            $category->active();
        } );
    }

    public function scopeHasActiveLeague( $query ) {
        return $query->whereHas( 'league', function ( $league ) {
            $league->active();
        } );
    }

    public function scopeDateTimeFilter( $query, $column ) {
        if ( ! request()->$column ) {
            return $query;
        }

        try {
            $date      = explode( '-', request()->$column );
            $startDate = Carbon::parse( trim( $date[0] ) )->format( 'Y-m-d' );
            $endDate   = @$date[1] ? Carbon::parse( trim( @$date[1] ) )->format( 'Y-m-d' ) : $startDate;
        } catch ( \Exception $e ) {
            throw ValidationException::withMessages( [ 'error' => 'Invalid date provided' ] );
        }

        return $query->whereDate( $column, '>=', $startDate )->whereDate( $column, '<=', $endDate );
    }

    public function playerImage() {
        return getImage( getFilePath( 'player' ) . '/' . $this->player_image, getFileSize( 'player' ) );
    }
}
