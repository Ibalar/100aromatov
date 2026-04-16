<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\FilterPage\Pages;

use App\MoonShine\Resources\FilterPage\FilterPageResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Throwable;

/**
 * @extends DetailPage<FilterPageResource>
 */
class FilterPageDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Slug', 'slug'),
            Text::make('Category', 'category.name_ru'),
            Json::make('Filter Data', 'filter_data')->object(),
            Text::make('H1 RU', 'h1_ru'),
            Text::make('H1 BY', 'h1_by'),
            Text::make('SEO Title RU', 'seo_title_ru'),
            Text::make('SEO Title BY', 'seo_title_by'),
            Textarea::make('SEO Desc RU', 'seo_description_ru'),
            Textarea::make('SEO Desc BY', 'seo_description_by'),
            Switcher::make('Indexable', 'is_indexable'),
            Switcher::make('Show in tiles', 'show_in_category_tiles'),
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

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [
            ...parent::topLayer(),
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer(),
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer(),
        ];
    }
}
