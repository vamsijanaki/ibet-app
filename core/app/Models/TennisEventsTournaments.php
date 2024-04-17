<?php

namespace App\Models;

use App\Traits\BindsDynamically;
use App\Traits\Searchable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class TennisEventsTournaments extends Model
{
    use HasFactory, BindsDynamically, Searchable;

    public $timestamps = true;

    protected $guarded = [];


}