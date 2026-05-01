<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Review\Pages;

use App\Models\Review;
use App\MoonShine\Resources\Product\ProductResource;
use App\MoonShine\Resources\Review\ReviewResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
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
                Text::make('Тип', formatted: static fn (Review $review) => $review->product_id ? 'Товар' : 'О магазине')
                    ->previewMode(),
                BelongsTo::make(
                    'Товар',
                    'product',
                    formatted: static fn ($product) => localizedField($product, 'name'),
                    resource: ProductResource::class,
                )->nullable(),
                Text::make('Имя пользователя', 'reviewer_name')
                    ->hint('Отображается на сайте как автор отзыва'),
                Text::make('Email автора', formatted: static fn (Review $review) => $review->customer?->email ?? '')
                    ->previewMode(),
                Number::make('Оценка', 'rating')->min(1)->max(5),
                Textarea::make('Текст', 'text'),
                Image::make('Фото', 'image')
                    ->dir('reviews')
                    ->disk('public')
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp'])
                    ->removable()
                    ->nullable(),
                Textarea::make('Ответ администратора', 'admin_reply'),
                Date::make('Дата отзыва', 'created_at')
                    ->format('d.m.Y H:i')
                    ->default(now()->toDateTimeString()),
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
        return [
            'reviewer_name' => ['nullable', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'text' => ['required', 'string'],
        ];
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
