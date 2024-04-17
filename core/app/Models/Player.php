<?php

namespace App\Models;

use App\Traits\BindsDynamically;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory, BindsDynamically, Searchable;

    protected $guarded = [];

    protected static $globalTable;

    protected $dates = ['birthdate'];

    protected $casts = [
        'draft' => 'object',
        'injuries' => 'object',
        'updated' => 'datetime:Y-m-d H:i:s'
    ];

    public static function setGlobalTable($table)
    {
        self::$globalTable = $table;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function games()
    {
        return $this->belongsToMany(Game::class);
    }

    public function team()
    {
        return $this->hasOne(Team::class, 'team_id', 'team_id');
    }

    public function injury()
    {
        $league = League::find($this->league_id);
        if ($league) {
            $instance = (new PlayerInjury())->setTable($league->slug . '_player_injuries');

            return $this->newHasOne($instance->newQuery(), $this, 'player_id', 'player_id', 'injury');
        }
    }

    public function getInjury()
    {
        try {
            $injuries = $this->injury->injuries;

            return $injuries[0];
        } catch (\Exception $ex) {
            return null;
        }
    }

    public function playerImage($at_listing = false)
    {

        if ($at_listing) {
            if (file_exists(getFilePath('player') . '/' . $this->image_path) && is_file(getFilePath('player') . '/' . $this->image_path)) {
                return asset(getFilePath('player') . '/' . $this->image_path);
            } else {
                return '';
            }
        }


        return getImage(getFilePath('player') . '/' . $this->image_path, getFileSize('player'));

    }
}
