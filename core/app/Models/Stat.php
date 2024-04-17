<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model {
    use HasFactory, Searchable, GlobalStatus;

    public function league()
    {
        return $this->belongsTo( League::class );
    }

    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_stat_relation');
    }
}
