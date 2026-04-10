<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Review\Pages;

use App\Models\Review;
use App\MoonShine\Resources\Product\ProductResource;
use App\MoonShine\Resources\Review\ReviewResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends FormPage<ReviewResource>
 */
class ReviewFormPage extends FormPage
{
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                BelongsTo::make(
                    'Товар',
                    'product',
                    formatted: static fn ($product) => localizedField($product, 'name'),
                    resource: ProductResource::class,
                ),
                Text::make('Автор', formatted: static fn (Review $review) => $review->author_name)
                    ->previewMode(),
                Text::make('Email автора', formatted: static fn (Review $review) => $review->customer?->email ?? '')
                    ->previewMode(),
                Number::make('Оценка', 'rating')->min(1)->max(5),
                Textarea::make('Текст', 'text'),
                Switcher::make('Одобрен', 'is_approved'),
            ]),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function formButtons(): ListOf
    {
        return parent::formButtons();
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [];
    }

    protected function modifyFormComponent(FormBuilderContract $component): FormBuilderContract
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
