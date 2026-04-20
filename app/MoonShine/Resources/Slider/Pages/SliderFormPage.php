<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Slider\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\Slider\SliderResource;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\Color;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;
use Throwable;


/**
 * @extends FormPage<SliderResource>
 */
class SliderFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                Image::make('Фоновое изображение', 'background_image')
                    ->dir('sliders')
                    ->disk('public')
                    ->removable()
                    ->required(fn() => $this->getResource()->isCreateFormPage()),
                Box::make('Русский язык', [
                    Text::make('Заголовок (RU)', 'title_ru')
                        ->nullable(),
                    Text::make('Подзаголовок (RU)', 'subtitle_ru')
                        ->nullable(),
                ]),


                Box::make('Беларуская мова', [
                    Text::make('Загаловак (BY)', 'title_be')
                        ->nullable(),
                    Text::make('Падзагаловак (BY)', 'subtitle_be')
                        ->nullable(),
                ]),

                // Настройки
                Color::make('Цвет текста', 'text_color')
                    ->default('#ffffff')
                    ->required(),

                Url::make('Ссылка кнопки', 'button_link')
                    ->nullable()
                    ->placeholder('Например: /category/shoes или /product/5'),

                Number::make('Порядок сортировки', 'sort_order')
                    ->default(0)
                    ->required(),

                Switcher::make('Активен', 'is_active')
                    ->default(true),
            ]),
        ];
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
