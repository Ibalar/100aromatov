<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Order\Pages;

use App\MoonShine\Resources\Order\OrderResource;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<OrderResource>
 */
class OrderIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Статус', 'status')->sortable(),
            Text::make('Телефон', 'phone')->sortable(),
            Text::make('Email', 'email'),
            Text::make('Сумма BYN', 'total_byn')->sortable(),
            Text::make('Промокод', 'promo_code'),
            Date::make('Создан', 'created_at')->format('d.m.Y H:i')->sortable(),
        ];
    }

    protected function filters(): iterable
    {
        return [
            Text::make('Статус', 'status'),
            Text::make('Телефон', 'phone'),
        ];
    }

    /**
     * @param TableBuilder $component
     */
    protected function modifyListComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }
}

