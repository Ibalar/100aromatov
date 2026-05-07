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
                        Text::make('Telegram URL', 'telegram_url')
                            ->hint('Например: https://t.me/username или https://t.me/+invite'),
                        Text::make('Viber URL', 'viber_url')
                            ->hint('Например: viber://chat?number=%2B375XXXXXXXXX или https://invite.viber.com/...'),
                        Text::make('WhatsApp URL', 'whatsapp_url')
                            ->hint('Например: https://wa.me/375XXXXXXXXX'),
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
                        Text::make('Google Reviews URL', 'google_reviews_url')
                            ->hint('Прямая ссылка на форму отзыва в Google'),
                        Text::make('Yandex Reviews URL', 'yandex_reviews_url')
                            ->hint('Прямая ссылка на форму отзыва в Яндекс Картах'),
                        Number::make('Рейтинг Яндекс Карт', 'yandex_rating')
                            ->step(0.1)
                            ->hint('Например: 4.9'),
                        Number::make('Количество отзывов (Яндекс)', 'yandex_reviews_count')
                            ->step(1)
                            ->hint('Например: 127'),
                        Textarea::make('Реквизиты', 'requisites')
                            ->hint('Можно указать УНП, расчетный счет, банк и другие данные'),
                    ]),
                ]),
                Tab::make('Метрики/счетчики', [
                    Box::make([
                        Textarea::make('Код перед </head>', 'metrics_head_code')
                            ->hint('Например: Google Tag Manager, Meta Pixel и другие скрипты в head'),
                        Textarea::make('Код сразу после <body>', 'metrics_body_start_code')
                            ->hint('Например: noscript часть GTM'),
                        Textarea::make('Код перед </body>', 'metrics_body_end_code')
                            ->hint('Дополнительный код счетчиков/метрик'),
                    ]),
                ]),
                Tab::make('Бегущая строка', [
                    Box::make([
                        Json::make('Пункты бегущей строки', 'infinite_slide_items')
                            ->fields([
                                Text::make('Иконка (класс)', 'icon')
                                    ->hint('Например: icon-Lightning-1')
                                    ->required(),
                                Text::make('Текст RU', 'text_ru')
                                    ->required(),
                                Text::make('Текст BY', 'text_by')
                                    ->required(),
                            ])
                            ->hint('Каждый пункт выводится как: иконка + текст'),
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
            'telegram_url' => ['nullable', 'string', 'max:255'],
            'viber_url' => ['nullable', 'string', 'max:255'],
            'whatsapp_url' => ['nullable', 'string', 'max:255'],
            'google_reviews_url' => ['nullable', 'url', 'max:255'],
            'yandex_reviews_url' => ['nullable', 'url', 'max:255'],
            'yandex_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'yandex_reviews_count' => ['nullable', 'integer', 'min:0'],
            'infinite_slide_items' => ['nullable', 'array'],
            'infinite_slide_items.*.icon' => ['nullable', 'string', 'max:128'],
            'infinite_slide_items.*.text_ru' => ['nullable', 'string', 'max:255'],
            'infinite_slide_items.*.text_by' => ['nullable', 'string', 'max:255'],
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
