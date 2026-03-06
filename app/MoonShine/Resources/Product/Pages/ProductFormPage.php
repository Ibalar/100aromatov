<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Product\Pages;

use App\MoonShine\Resources\Brand\BrandResource;
use App\MoonShine\Resources\Category\CategoryResource;
use App\MoonShine\Resources\ProductImage\ProductImageResource;
use App\MoonShine\Resources\ProductVariant\ProductVariantResource;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\Product\ProductResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Html;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Throwable;


/**
 * @extends FormPage<ProductResource>
 */
class ProductFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Tabs::make([
                Tab::make('Основное', [
                    ID::make(),
                    Flex::make([
                        Text::make('Название RU', 'name_ru')
                            ->when(
                                fn() => $this->getResource()->isCreateFormPage(),
                                fn(Text $field) => $field->reactive(),
                                fn(Text $field) => $field
                            )
                            ->required(),
                        Text::make('Назва BY', 'name_by')->required(),
                    ])
                        ->unwrap()
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                    Flex::make([
                        Slug::make('Slug')
                            ->unique()
                            ->locked()
                            ->when(
                                fn() => $this->getResource()->isCreateFormPage(),
                                fn(Slug $field) => $field->from('name_ru')->live(),
                                fn(Slug $field) => $field->readonly()
                            ),
                        BelongsTo::make('Категория', 'category', resource: CategoryResource::class),
                    ])
                        ->unwrap()
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                    Switcher::make('Активен', 'is_active'),
                    Switcher::make('В избранных (Featured)', 'is_featured'),

                    BelongsTo::make('Бренд', 'brand', resource: BrandResource::class)
                        ->searchable()
                        ->creatable(
                            button: ActionButton::make('Добавить бренд', '')
                        ),
                ]),
                Tab::make('Описание', [
                    TinyMce::make('Описание RU', 'description_ru'),
                    TinyMce::make('Описание BY', 'description_by'),
                ]),
                Tab::make('Характеристики', [
                    Text::make('Страна', 'country'),
                    Text::make('Концентрация', 'concentration'),
                    Text::make('Пол', 'gender'),
                ]),
            ])->vertical(),

            HasMany::make('Изображения', 'images', resource: ProductImageResource::class)
                ->creatable(
                    button: ActionButton::make('Добавить изображение', '')
                ),

            HasMany::make('Варианты', 'variants', resource: ProductVariantResource::class)
                ->creatable(
                    button: ActionButton::make('Добавить вариант', '')
                ),
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

    /**
     * @param  FormBuilder  $component
     *
     * @return FormBuilder
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
            ...parent::topLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }
}
