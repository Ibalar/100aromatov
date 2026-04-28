<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Order\Pages;

use App\MoonShine\Resources\Order\OrderResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Text;
use Throwable;

/**
 * @extends DetailPage<OrderResource>
 */
class OrderDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Статус', 'status'),
            Text::make('Телефон', 'phone'),
            Text::make('Перезвон', 'call_preference'),
            Text::make('Email', 'email'),
            Text::make('Сумма BYN', 'total_byn'),
            Text::make('Сумма USD', 'total_usd'),
            Text::make('Промокод', 'promo_code'),
            Text::make('Скидка USD', 'discount_usd'),
            Textarea::make('Позиции заказа', 'items_summary'),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function modifyDetailComponent(ComponentContract $component): ComponentContract
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

