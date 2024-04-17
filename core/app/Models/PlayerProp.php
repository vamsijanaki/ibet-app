<?php

namespace App\Models;

use App\Traits\BindsDynamically;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerProp extends Model
{
    use HasFactory, BindsDynamically;

    protected $guarded = [];
}
