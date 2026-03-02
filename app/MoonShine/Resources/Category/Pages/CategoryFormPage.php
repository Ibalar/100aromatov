<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Category\Pages;

use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\Category\CategoryResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Throwable;


/**
 * @extends FormPage<CategoryResource>
 */
class CategoryFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Tabs::make([
                Tab::make('Основные данные', [
                    ID::make(),
                    Grid::make([
                        Column::make(
                            [
                                Text::make('Название RU', 'name_ru')
                                    ->when(
                                        fn() => $this->getResource()->isCreateFormPage(),
                                        fn(Text $field) => $field->reactive(),
                                        fn(Text $field) => $field
                                    )
                                    ->required(),
                            ],
                            colSpan: 6,
                        ),
                        Column::make(
                            [
                                Text::make('Назва BY', 'name_by'),
                            ],
                            colSpan: 6,
                        ),
                        Column::make(
                            [
                                BelongsTo::make('Родитель', 'parent', resource: CategoryResource::class)
                                    ->nullable(),
                            ],
                            colSpan: 6,
                        ),
                        Column::make(
                            [
                                Slug::make('Slug')
                                    ->unique()
                                    ->locked()
                                    ->when(
                                        fn() => $this->getResource()->isCreateFormPage(),
                                        fn(Slug $field) => $field->from('name_ru')->live(),
                                        fn(Slug $field) => $field->readonly()
                                    ),
                            ],
                            colSpan: 6,
                        ),
                    ]),
                    Number::make('Сортировка', 'sort_order')
                        ->default(0)
                        ->sortable(),
                    Switcher::make('Активна', 'is_active'),
                ]),
                Tab::make('Описание', [
                    TinyMce::make('Описание RU', 'description_ru'),
                    TinyMce::make('Апісанне BY', 'description_by'),
                ]),
                Tab::make('СЕО', [
                    Flex::make([
                        Text::make('SEO Title RU', 'seo_title_ru'),
                        Text::make('SEO Title BY', 'seo_title_by'),
                    ])
                        ->unwrap()
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                    Flex::make([
                        Textarea::make('SEO Description RU', 'seo_description_ru'),
                        Textarea::make('SEO Description BY', 'seo_description_by'),
                    ])
                        ->unwrap()
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                    Flex::make([
                        Textarea::make('SEO Текст RU', 'seo_text_ru'),
                        Textarea::make('SEO Тэкст BY', 'seo_text_by'),
                    ])
                        ->unwrap()
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                ]),
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
