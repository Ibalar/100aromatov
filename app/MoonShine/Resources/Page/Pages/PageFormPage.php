<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Page\Pages;

use App\MoonShine\Resources\Page\PageResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\ListOf;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Throwable;

/**
 * @extends FormPage<PageResource>
 */
class PageFormPage extends FormPage
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
                    Text::make('Название RU', 'name_ru')
                        ->when(
                            fn() => $this->getResource()->isCreateFormPage(),
                            fn(Text $field) => $field->reactive(),
                            fn(Text $field) => $field
                        )
                        ->required(),
                    Text::make('Назва BY', 'name_by')->required(),
                    Slug::make('Slug')
                        ->unique()
                        ->locked()
                        ->when(
                            fn() => $this->getResource()->isCreateFormPage(),
                            fn(Slug $field) => $field->from('name_ru')->live(),
                            fn(Slug $field) => $field->readonly()
                        ),
                    Number::make('Порядок сортировки', 'sort_order')
                        ->default(0)
                        ->sortable(),
                    Switcher::make('Активна', 'is_active'),
                    Switcher::make('Выводить в меню', 'show_in_menu'),
                ]),
                Tab::make('Описание', [
                    TinyMce::make('Описание RU', 'description_ru'),
                    TinyMce::make('Апісанне BY', 'description_by'),
                ]),
                Tab::make('SEO', [
                    Text::make('SEO Title RU', 'seo_title_ru'),
                    Text::make('SEO Title BY', 'seo_title_by'),
                    Textarea::make('SEO Description RU', 'seo_description_ru'),
                    Textarea::make('SEO Description BY', 'seo_description_by'),
                ]),
            ])->vertical(),
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
