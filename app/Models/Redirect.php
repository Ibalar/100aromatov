<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = [
        'old_url',
        'new_url',
        'type',
        'hits',
    ];

    protected $casts = [
        'type' => 'integer',
        'hits' => 'integer',
    ];

    public function incrementHits(): void
    {
        $this->increment('hits');
    }
}
