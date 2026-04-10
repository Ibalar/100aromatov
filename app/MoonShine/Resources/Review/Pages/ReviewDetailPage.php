<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Review\Pages;

use App\Models\Review;
use App\MoonShine\Resources\Review\ReviewResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends DetailPage<ReviewResource>
 */
class ReviewDetailPage extends DetailPage
{
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Товар', formatted: static fn (Review $review) => localizedField($review->product, 'name')),
            Text::make('Автор', formatted: static fn (Review $review) => $review->author_name),
            Text::make('Email автора', formatted: static fn (Review $review) => $review->customer?->email ?? ''),
            Text::make('Оценка', 'rating'),
            Textarea::make('Текст', 'text'),
            Switcher::make('Одобрен', 'is_approved'),
            Date::make('Создан', 'created_at')->format('d.m.Y H:i'),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function modifyDetailComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    protected function topLayer(): array
    {
        return [
            ...parent::topLayer(),
        ];
    }

    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer(),
        ];
    }

    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer(),
        ];
    }
}
