<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Product\Pages;

use App\Models\Brand;
use App\Models\Product;
use App\MoonShine\Resources\Product\ProductResource;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * @extends IndexPage<ProductResource>
 */
class ProductIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        $resource = $this->getResource();

        return [
            ID::make()->sortable(),
            Text::make('Название', 'name_ru')
                ->link(
                    static fn (string $value, Text $field) => $resource->getFormPageUrl(
                        $field->getData()?->getKey()
                    ),
                    blank: false
                ),
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
            Select::make('Бренд', 'brand_id')
                ->options(
                    Cache::remember('brand_options', 3600, fn() => Brand::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray())
                )
                ->native()
                ->nullable()
                ->placeholder('Все бренды'),

            Switcher::make('Активен', 'is_active'),
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

    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyListComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    protected function modifyDetailButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button
            ->setUrl(
                static fn (?Product $product): string => route('product.show', $product?->slug)
            )
            ->blank()
            ->disableAsync();
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
