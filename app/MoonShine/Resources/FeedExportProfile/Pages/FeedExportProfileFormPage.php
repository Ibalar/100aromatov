<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\FeedExportProfile\Pages;

use App\Models\Brand;
use App\Models\Category;
use App\MoonShine\Resources\FeedExportProfile\FeedExportProfileResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<FeedExportProfileResource>
 */
class FeedExportProfileFormPage extends FormPage
{
    protected function fields(): iterable
    {
        $categoryOptions = Category::query()
            ->orderBy('name_ru')
            ->get()
            ->mapWithKeys(static fn (Category $category): array => [
                $category->slug => localizedField($category, 'name') . ' [' . $category->slug . ']',
            ])
            ->toArray();

        $brandOptions = Brand::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(static fn (Brand $brand): array => [
                $brand->slug => $brand->name . ' [' . $brand->slug . ']',
            ])
            ->toArray();

        return [
            Box::make([
                ID::make(),
                Text::make('Название профиля', 'name')->required(),
                Select::make('Платформа', 'platform')
                    ->options([
                        'google' => 'Google Merchant (RSS/XML)',
                        'yandex' => 'Yandex Market (YML)',
                    ])
                    ->required(),
                Text::make('Название магазина', 'shop_name')
                    ->hint('Если пусто, будет использовано APP_NAME'),
                Text::make('Название компании', 'company_name')
                    ->hint('Если пусто, будет использовано APP_NAME'),
                Select::make('Язык контента', 'language')
                    ->options([
                        'ru' => 'Русский',
                        'by' => 'Белорусский',
                    ])
                    ->required(),
                Text::make('Валюта', 'currency')
                    ->default('BYN')
                    ->required(),
                Switcher::make('Только в наличии', 'only_in_stock')->default(true),
                Switcher::make('Включать неактивные товары', 'include_inactive_products')->default(false),
                Number::make('Мин. цена BYN', 'min_price_byn')->step(0.01),
                Number::make('Макс. цена BYN', 'max_price_byn')->step(0.01),
                Number::make('Лимит товаров (пусто = без лимита)', 'max_items')->min(1),
                Select::make('Категории для включения', 'include_category_slugs')
                    ->options($categoryOptions)
                    ->multiple()
                    ->searchable()
                    ->hint('Выберите категории, которые должны попасть в фид'),
                Select::make('Категории для исключения', 'exclude_category_slugs')
                    ->options($categoryOptions)
                    ->multiple()
                    ->searchable(),
                Select::make('Бренды для включения', 'include_brand_slugs')
                    ->options($brandOptions)
                    ->multiple()
                    ->searchable(),
                Select::make('Бренды для исключения', 'exclude_brand_slugs')
                    ->options($brandOptions)
                    ->multiple()
                    ->searchable(),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'in:google,yandex'],
            'shop_name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'language' => ['required', 'in:ru,by'],
            'currency' => ['required', 'string', 'max:8'],
            'only_in_stock' => ['boolean'],
            'include_inactive_products' => ['boolean'],
            'min_price_byn' => ['nullable', 'numeric', 'min:0'],
            'max_price_byn' => ['nullable', 'numeric', 'min:0'],
            'max_items' => ['nullable', 'integer', 'min:1'],
            'include_category_slugs' => ['nullable', 'array'],
            'include_category_slugs.*' => ['string', 'max:255'],
            'exclude_category_slugs' => ['nullable', 'array'],
            'exclude_category_slugs.*' => ['string', 'max:255'],
            'include_brand_slugs' => ['nullable', 'array'],
            'include_brand_slugs.*' => ['string', 'max:255'],
            'exclude_brand_slugs' => ['nullable', 'array'],
            'exclude_brand_slugs.*' => ['string', 'max:255'],
        ];
    }
}
