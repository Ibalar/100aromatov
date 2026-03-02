<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    protected $fillable = [
        'code',
        'name_ru',
        'name_by',
        'is_filterable',
        'is_seo',
        'sort_order',
    ];

    protected $casts = [
        'is_filterable' => 'boolean',
        'is_seo' => 'boolean',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
}
