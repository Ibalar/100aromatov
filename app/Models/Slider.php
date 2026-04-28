<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    const TYPE_SLIDE = 'slide';
    const TYPE_BANNER = 'banner';

    protected $fillable = [
        'type',
        'background_image',
        'title_ru',
        'title_be',
        'subtitle_ru',
        'subtitle_be',
        'text_color',
        'button_link',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Аксессоры для получения данных на текущем языке
    public function getTitleAttribute(): ?string
    {
        $locale = app()->getLocale();
        $field = 'title_' . $locale;
        return $this->$field ?? $this->title_ru;
    }

    public function getSubtitleAttribute(): ?string
    {
        $locale = app()->getLocale();
        $field = 'subtitle_' . $locale;
        return $this->$field ?? $this->subtitle_ru;
    }

    // Проверка наличия кнопки
    public function hasButton(): bool
    {
        return !empty(trim((string) $this->button_link));
    }

    public function getButtonUrlAttribute(): ?string
    {
        $link = trim((string) $this->button_link);

        if ($link === '') {
            return null;
        }

        if (filter_var($link, FILTER_VALIDATE_URL)) {
            return $link;
        }

        return $link;
    }

    // Получение URL изображения
    public function getImageUrlAttribute(): string
    {
        return $this->background_image ? asset('storage/' . $this->background_image) : '';
    }

    public function scopeSlides($query)
    {
        return $query->where('type', self::TYPE_SLIDE);
    }

    public function scopeBanners($query)
    {
        return $query->where('type', self::TYPE_BANNER);
    }
}
