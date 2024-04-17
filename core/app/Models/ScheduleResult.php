<?php

namespace App\Models;

use App\Traits\BindsDynamically;
use App\Traits\Searchable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ScheduleResult extends Model {
    use HasFactory, BindsDynamically, Searchable;

    protected $guarded = [];

    protected $casts = [
        'scheduled'      => 'datetime:Y-m-d H:i:s',
        'venue_location' => 'object',
        'broadcasts'     => 'object'
    ];

    public function league() {
        return $this->belongsTo( League::class );
    }

    public function away_team() {
        return $this->hasOne( Team::class, 'team_id', 'schedule_awayTeam_id' );
    }

    public function home_team() {
        return $this->hasOne( Team::class, 'team_id', 'schedule_homeTeam_id' );
    }

    public function games() {
        return $this->hasMany( Game::class, 'week', 'schedule_week' );
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

    protected function gameStarts(): Attribute {
        return Attribute::make(
            get: fn( mixed $value, array $attributes ) => Carbon::parse( $attributes['schedule_startTime'] )->toDayDateTimeString(),
        );
    }
}
