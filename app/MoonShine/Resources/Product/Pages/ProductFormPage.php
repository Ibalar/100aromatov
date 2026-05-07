<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Product\Pages;

use App\Models\Product;
use App\MoonShine\Resources\AttributeValue\AttributeValueResource;
use App\MoonShine\Resources\Brand\BrandResource;
use App\MoonShine\Resources\Category\CategoryResource;
use App\MoonShine\Resources\Product\ProductResource;
use App\MoonShine\Resources\ProductImage\ProductImageResource;
use App\MoonShine\Resources\ProductVariant\ProductVariantResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\ListOf;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
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
                                fn () => $this->getResource()->isCreateFormPage(),
                                fn (Text $field) => $field->reactive(),
                                fn (Text $field) => $field
                            )
                            ->required(),
                        Text::make('Название BY', 'name_by')->required(),
                    ])
                        ->unwrap()
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                    Flex::make([
                        Slug::make('Slug')
                            ->unique()
                            ->locked()
                            ->when(
                                fn () => $this->getResource()->isCreateFormPage(),
                                fn (Slug $field) => $field->from('name_ru')->live(),
                                fn (Slug $field) => $field->readonly()
                            ),
                        BelongsTo::make('Категория', 'category', resource: CategoryResource::class),
                    ])
                        ->unwrap()
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                    Text::make('Старый URL', 'old_url')
                        ->hint('Например: /old-product-slug или /catalog/old-product-slug'),
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
                    Flex::make([
                        Text::make('Страна RU', 'country'),
                        Text::make('Краіна BY', 'country_by'),
                    ])
                        ->unwrap()
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                    Flex::make([
                        Text::make('Концентрация RU', 'concentration'),
                        Text::make('Канцэнтрацыя BY', 'concentration_by'),
                    ])
                        ->unwrap()
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                    Flex::make([
                        Text::make('Пол RU', 'gender'),
                        Text::make('Пол BY', 'gender_by'),
                    ])
                        ->unwrap()
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
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
                        Textarea::make('SEO Description RU', 'seo_description_ru'),
                        Textarea::make('SEO Description BY', 'seo_description_by'),
                    ])
                        ->unwrap()
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                ]),
            ])->vertical(),

            HasMany::make('Изображения', 'images', resource: ProductImageResource::class)
                ->fields([
                    Image::make('Изображение', 'path'),
                    Number::make('Сортировка', 'sort_order'),
                ])
                ->creatable(),

            HasMany::make('Варианты', 'variants', resource: ProductVariantResource::class)
                ->fields([
                    Text::make('SKU', 'sku')->required(),
                    Text::make('Объём ML', 'volume_ml'),
                    Number::make('Цена USD', 'price_usd')
                        ->required()
                        ->min(0)
                        ->step(0.01)
                        ->updateOnPreview(),
                    Switcher::make('Тестер', 'is_tester')->updateOnPreview(),
                    Switcher::make('Распив', 'is_raspiv')->updateOnPreview(),
                    Switcher::make('Отливант', 'is_exclusive')->updateOnPreview(),
                    Switcher::make('Активен', 'is_active')->updateOnPreview(),
                ])
                ->creatable(),

            BelongsToMany::make('Характеристики', 'attributeValues', resource: AttributeValueResource::class)
                ->valuesQuery(static fn ($query) => $query->orderBy('value_ru'))
                ->creatable(
                    button: ActionButton::make('Добавить характеристику', '')
                ),
        ];
    }

    protected function buttons(): ListOf
    {
        $buttons = [
            $this->makeCatalogButton(),
            $this->makeSaveButton(),
        ];

        if ($this->isItemExists()) {
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
        return $component
            ->customAttributes([
                'id' => $this->getTopSubmitFormId(),
            ])
            ->hideSubmit();
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

    protected function getTopSubmitFormId(): string
    {
        return 'product-resource-form';
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
