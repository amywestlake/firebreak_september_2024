<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'year',
        'image_base',
        'image_relative',
        'image_main',
        'image_thumb',
        'image_preload',
        'full_description',
        'producer',
        'writer',
        'url'
    ];

    public function pollResults()
    {
        return $this->hasMany(PollResult::class);
    }

    public function directors()
    {
        return $this->belongsToMany(Director::class);
    }

    public function actors()
    {
        return $this->belongsToMany(Actor::class);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }
}
