<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Brand\Pages;

use App\MoonShine\Resources\Brand\BrandResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\ListOf;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Throwable;

/**
 * @extends FormPage<BrandResource>
 */
class BrandFormPage extends FormPage
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
                                Text::make('Название', 'name')
                                    ->when(
                                        fn () => $this->getResource()->isCreateFormPage(),
                                        fn (Text $field) => $field->reactive(),
                                        fn (Text $field) => $field
                                    )
                                    ->required(),
                            ],
                            colSpan: 6,
                        ),
                        Column::make(
                            [
                                Slug::make('Slug', 'slug')
                                    ->unique()
                                    ->locked()
                                    ->when(
                                        fn () => $this->getResource()->isCreateFormPage(),
                                        fn (Slug $field) => $field->from('name')->live(),
                                        fn (Slug $field) => $field->readonly()
                                    ),
                            ],
                            colSpan: 6,
                        ),
                        Column::make(
                            [
                                Text::make('Страна', 'country'),
                            ],
                            colSpan: 6,
                        ),
                        Column::make(
                            [
                                Image::make('Логотип', 'logo')
                                    ->dir('brand/logo')
                                    ->disk('public')
                                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg', 'avif'])
                                    ->removable()
                                    ->nullable(),
                            ],
                            colSpan: 6,
                        ),
                    ]),
                    Switcher::make('Активна', 'is_active'),
                ]),
                Tab::make('Описание', [
                    TinyMce::make('Описание RU', 'description_ru'),
                    TinyMce::make('Апісанне BY', 'description_by'),
                ]),
                Tab::make('SEO', [
                    Flex::make([
                        Text::make('SEO Title RU', 'seo_title_ru'),
                        Text::make('SEO Title BY', 'seo_title_by'),
                    ])
                        ->unwrap()
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                    Flex::make([
                        Text::make('H1 Title RU', 'h1_title_ru'),
                        Text::make('H1 Title BY', 'h1_title_by'),
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
                ]),
            ]),
        ];
    }

    protected function buttons(): ListOf
    {
        $buttons = [
            $this->makeCatalogButton(),
        ];

        if ($this->isItemExists()) {
            $buttons[] = $this->makeSaveButton();
            $buttons[] = $this->modifyDetailButton(
                $this->getResource()->getDetailButton()
            );
            $buttons[] = $this->modifyDeleteButton(
                $this->getResource()->getDeleteButton(
                    redirectAfterDelete: $this->getResource()->getRedirectAfterDelete(),
                    isAsync: false,
                )
            );
        }

        return new ListOf(ActionButtonContract::class, $buttons);
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
        $component = $component->customAttributes([
            'id' => $this->getTopSubmitFormId(),
        ]);

        if ($this->isItemExists()) {
            return $component->hideSubmit();
        }

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

    protected function getTopSubmitFormId(): string
    {
        return 'brand-resource-form';
    }

    protected function makeSaveButton(): ActionButton
    {
        return ActionButton::make(__('moonshine::ui.save'))
            ->primary()
            ->customAttributes([
                'type' => 'submit',
                'form' => $this->getTopSubmitFormId(),
            ]);
    }

    protected function makeCatalogButton(): ActionButton
    {
        return ActionButton::make('Назад', $this->getResource()->getIndexPageUrl())
            ->secondary();
    }
}
