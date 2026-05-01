<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\PromoCode\Pages;

use App\MoonShine\Resources\PromoCode\PromoCodeResource;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends DetailPage<PromoCodeResource>
 */
class PromoCodeDetailPage extends DetailPage
{
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Код', 'code'),
            Text::make('Тип', 'type'),
            Number::make('Значение', 'value'),
            Number::make('Мин. сумма USD', 'min_order_usd'),
            Number::make('Лимит общий', 'usage_limit'),
            Number::make('Лимит на пользователя', 'usage_per_user'),
            Number::make('Использовано', 'used_count'),
            Date::make('Активен с', 'active_from')->format('d.m.Y H:i'),
            Date::make('Активен до', 'active_to')->format('d.m.Y H:i'),
            Switcher::make('Активен', 'is_active'),
        ];
    }
}
