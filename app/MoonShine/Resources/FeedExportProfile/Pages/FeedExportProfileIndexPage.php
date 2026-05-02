<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\FeedExportProfile\Pages;

use App\Models\FeedExportProfile;
use App\MoonShine\Resources\FeedExportProfile\FeedExportProfileResource;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<FeedExportProfileResource>
 */
class FeedExportProfileIndexPage extends IndexPage
{
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Название', 'name')->sortable(),
            Text::make('Платформа', 'platform'),
            Switcher::make('Только в наличии', 'only_in_stock'),
            Text::make('Скачать', formatted: static fn (FeedExportProfile $profile): string => 'Скачать')
                ->link(
                    static fn (string $value, Text $field) => route('admin.feed-profiles.download', $field->getData()?->getKey()),
                    blank: false
                ),
        ];
    }
}
