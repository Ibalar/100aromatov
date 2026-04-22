<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Review\Pages;

use App\Models\Review;
use App\MoonShine\Resources\Review\ReviewResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<ReviewResource>
 */
class ReviewIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Тип', formatted: static fn (Review $review) => $review->product_id ? 'Товар' : 'О магазине'),
            Text::make('Товар', formatted: static fn (Review $review) => $review->product ? localizedField($review->product, 'name') : $review->target_label),
            Text::make('Автор', formatted: static fn (Review $review) => $review->author_name),
            Image::make('Фото', 'image'),
            Text::make('Оценка', 'rating')->sortable()->badge(Color::YELLOW),
            Switcher::make('Одобрен', 'is_approved'),
            Date::make('Создан', 'created_at')->format('d.m.Y H:i')->sortable(),
        ];
    }

    protected function filters(): iterable
    {
        return [
            Text::make('Оценка', 'rating'),
            Text::make('Текст', 'text'),
            Switcher::make('Одобрен', 'is_approved'),
        ];
    }

    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->columnSelection()->useSharedModal();
    }
}
