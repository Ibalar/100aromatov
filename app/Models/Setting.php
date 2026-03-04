<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'usd_rate',
        'telegram_bot_token',
        'telegram_chat_id',
    ];

    protected $casts = [
        'usd_rate' => 'decimal:4',
    ];

    /*
    |--------------------------------------------------------------------------
    | Singleton accessor
    |--------------------------------------------------------------------------
    */

    public static function getSettings(): self
    {
        return static::firstOrCreate([]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function convertUsdToByn(float $usd): float
    {
        return round($usd * $this->usd_rate, 2);
    }
}
