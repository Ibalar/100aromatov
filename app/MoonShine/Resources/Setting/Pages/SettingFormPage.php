<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Setting\Pages;

use App\MoonShine\Resources\Setting\SettingResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Throwable;

/**
 * @extends FormPage<SettingResource>
 */
class SettingFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     * @throws Throwable
     */
    protected function fields(): iterable
    {
        return [
            Tabs::make([
                Tab::make('Основные', [
                    Box::make([
                        ID::make(),
                        Number::make('Курс пересчета', 'usd_rate')
                            ->step(0.0001),
                        Text::make('Токен Telegram', 'telegram_bot_token'),
                        Text::make('ID чата Telegram', 'telegram_chat_id'),
                    ]),
                ]),
                Tab::make('Контакты', [
                    Box::make([
                        Text::make('Email', 'email')
                            ->hint('Например: info@example.com'),
                        Json::make('Телефоны', 'phones')
                            ->fields([
                                Text::make('Подпись оператора', 'label')
                                    ->hint('Например: A1, МТС, life:)'),
                                Text::make('Номер телефона', 'number')
                                    ->required(),
                                Image::make('Иконка оператора', 'icon')
                                    ->dir('settings/phones')
                                    ->disk('public')
                                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp', 'svg', 'avif'])
                                    ->removable()
                                    ->nullable(),
                            ]),
                        Textarea::make('Адрес', 'address'),
                        Text::make('Ссылка на карту', 'address_map_url')
                            ->hint('Необязательно. Например ссылка на Google Maps или Яндекс Карты'),
                        Text::make('Instagram URL', 'instagram_url'),
                        Textarea::make('Реквизиты', 'requisites')
                            ->hint('Можно указать УНП, расчетный счет, банк и другие данные'),
                    ]),
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
        return [
            'email' => ['nullable', 'email'],
        ];
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
