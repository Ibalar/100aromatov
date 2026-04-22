<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AttributeValue\Pages;

use App\MoonShine\Resources\AttributeValue\AttributeValueResource;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Support\Enums\Color;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use Throwable;

/**
 * @extends IndexPage<AttributeValueResource>
 */
class AttributeValueIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Атрибут', 'attribute.name_ru')->sortable(),
            Text::make('Slug', 'slug')->sortable(),
            Text::make('Значение RU', 'value_ru')->sortable(),
            Text::make('Значение BY', 'value_by')->sortable(),
            Text::make('Сортировка', 'sort_order')->sortable()->badge(Color::GRAY),
        ];
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    /**
     * @return list<FieldContract>
     */
    protected function filters(): iterable
    {
        return [
            Text::make('Атрибут', 'attribute.name_ru'),
            Text::make('Slug', 'slug'),
            Text::make('Значение RU', 'value_ru'),
            Text::make('Значение BY', 'value_by'),
        ];
    }

    /**
     * @return list<QueryTag>
     */
    protected function queryTags(): array
    {
        return [];
    }

    /**
     * @return list<Metric>
     */
    protected function metrics(): array
    {
        return [];
    }

    protected function modifyListComponent(ComponentContract $component): ComponentContract
    {
        return $component->columnSelection()->simplePagination()->useSharedModal();
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
