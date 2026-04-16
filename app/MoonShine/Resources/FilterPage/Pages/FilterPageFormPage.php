<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\FilterPage\Pages;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\MoonShine\Resources\Category\CategoryResource;
use App\MoonShine\Resources\FilterPage\FilterPageResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Throwable;

/**
 * @extends FormPage<FilterPageResource>
 */
class FilterPageFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        $brandOptions = Brand::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $attributeOptions = Attribute::query()
            ->orderBy('sort_order')
            ->orderBy('name_ru')
            ->pluck('name_ru', 'id')
            ->toArray();

        $attributeValueOptions = AttributeValue::query()
            ->with('attribute:id,name_ru')
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(static function (AttributeValue $value): array {
                $attributeName = $value->attribute?->name_ru ?? 'Attribute';

                return [
                    (int) $value->id => sprintf('%s: %s', $attributeName, (string) $value->value_ru),
                ];
            })
            ->toArray();

        return [
            Box::make([
                ID::make(),

                BelongsTo::make('Категория', 'category', resource: CategoryResource::class),

                Slug::make('Slug', 'slug')
                    ->unique(),

                Json::make('Filter Data', 'filter_data')
                    ->object()
                    ->fields([
                        Select::make('Brands', 'brand')
                            ->options($brandOptions)
                            ->multiple()
                            ->searchable(),

                        Number::make('Min Price USD', 'min_price')
                            ->min(0)
                            ->step(0.01)
                            ->nullable(),

                        Number::make('Max Price USD', 'max_price')
                            ->min(0)
                            ->step(0.01)
                            ->nullable(),

                        Json::make('Attributes', 'attributes')
                            ->fields([
                                Select::make('Attribute', 'attribute_id')
                                    ->options($attributeOptions)
                                    ->searchable(),

                                Select::make('Values', 'value_ids')
                                    ->options($attributeValueOptions)
                                    ->multiple()
                                    ->searchable(),
                            ])
                            ->creatable()
                            ->reorderable(false),
                    ]),

                Text::make('H1 RU', 'h1_ru'),
                Text::make('H1 BY', 'h1_by'),
                Text::make('SEO Title RU', 'seo_title_ru'),
                Text::make('SEO Title BY', 'seo_title_by'),
                Textarea::make('SEO Desc RU', 'seo_description_ru'),
                Textarea::make('SEO Desc BY', 'seo_description_by'),
                Textarea::make('SEO Text RU', 'seo_text_ru'),
                Textarea::make('SEO Text BY', 'seo_text_by'),

                Switcher::make('Индексируемая', 'is_indexable'),
                Switcher::make('Показывать в плитке тегов', 'show_in_category_tiles'),
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
            'category_id' => ['required', 'exists:categories,id'],
            'slug' => ['required', 'max:255', 'unique:filter_pages,slug,' . ($item->getKey() ?? 'null')],
            'filter_data' => ['required', 'array'],
            'filter_data.brand' => ['nullable', 'array'],
            'filter_data.brand.*' => ['integer', 'exists:brands,id'],
            'filter_data.min_price' => ['nullable', 'numeric', 'min:0'],
            'filter_data.max_price' => ['nullable', 'numeric', 'min:0', 'gte:filter_data.min_price'],
            'filter_data.attributes' => ['nullable', 'array'],
            'filter_data.attributes.*.attribute_id' => ['required_with:filter_data.attributes', 'integer', 'exists:attributes,id'],
            'filter_data.attributes.*.value_ids' => ['required_with:filter_data.attributes', 'array'],
            'filter_data.attributes.*.value_ids.*' => ['integer', 'exists:attribute_values,id'],
            'show_in_category_tiles' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @param  FormBuilder  $component
     */
    protected function modifyFormComponent(FormBuilderContract $component): FormBuilderContract
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
