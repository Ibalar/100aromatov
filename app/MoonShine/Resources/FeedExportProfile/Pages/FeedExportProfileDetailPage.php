<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\FeedExportProfile\Pages;

use App\Models\FeedExportProfile;
use App\MoonShine\Resources\FeedExportProfile\FeedExportProfileResource;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends DetailPage<FeedExportProfileResource>
 */
class FeedExportProfileDetailPage extends DetailPage
{
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Название', 'name'),
            Text::make('Платформа', 'platform'),
            Text::make('Язык', 'language'),
            Text::make('Валюта', 'currency'),
            Switcher::make('Только в наличии', 'only_in_stock'),
            Switcher::make('Включать неактивные товары', 'include_inactive_products'),
            Text::make('Мин. цена BYN', 'min_price_byn'),
            Text::make('Макс. цена BYN', 'max_price_byn'),
            Text::make('Лимит товаров', 'max_items'),
            Textarea::make('Категории включить', formatted: static fn (FeedExportProfile $profile): string => implode("\n", $profile->include_category_slugs ?? [])),
            Textarea::make('Категории исключить', formatted: static fn (FeedExportProfile $profile): string => implode("\n", $profile->exclude_category_slugs ?? [])),
            Textarea::make('Бренды включить', formatted: static fn (FeedExportProfile $profile): string => implode("\n", $profile->include_brand_slugs ?? [])),
            Textarea::make('Бренды исключить', formatted: static fn (FeedExportProfile $profile): string => implode("\n", $profile->exclude_brand_slugs ?? [])),
            Text::make('Скачать файл', formatted: static fn (FeedExportProfile $profile): string => route('admin.feed-profiles.download', $profile->id))
                ->link(
                    static fn (string $value, Text $field) => route('admin.feed-profiles.download', $field->getData()?->getKey()),
                    blank: false
                ),
        ];
    }
}
