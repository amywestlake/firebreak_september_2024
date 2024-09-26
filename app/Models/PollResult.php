<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'film_id',
        'rank',
        'tied',
        'category',
    ];

    public function film()
    {
        return $this->belongsTo(Film::class);
    }
}
