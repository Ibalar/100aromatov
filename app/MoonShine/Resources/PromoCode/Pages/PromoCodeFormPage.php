<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\PromoCode\Pages;

use App\Models\PromoCode;
use App\MoonShine\Resources\PromoCode\PromoCodeResource;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<PromoCodeResource>
 */
class PromoCodeFormPage extends FormPage
{
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Код', 'code')
                    ->default(self::generateUniqueCode())
                    ->required()
                    ->hint('При создании поле заполняется автоматически случайным уникальным кодом. Либо можете удалить случайный код и ввести свой уникальный ПРОМОКОД'),
                Select::make('Тип', 'type')
                    ->options([
                        'percent' => 'Процент',
                        'fixed' => 'Фиксированная сумма (USD)',
                    ])
                    ->default('percent')
                    ->required(),
                Number::make('Значение', 'value')->required(),
                Number::make('Мин. сумма заказа USD', 'min_order_usd')->nullable(),
                Number::make('Лимит использований (общий)', 'usage_limit')->nullable(),
                Number::make('Лимит использований на пользователя', 'usage_per_user')->nullable(),
                Number::make('Счетчик использований', 'used_count')
                    ->default(0)
                    ->readonly(),
                Date::make('Активен с', 'active_from')->format('d.m.Y H:i'),
                Date::make('Активен до', 'active_to')->format('d.m.Y H:i'),
                Switcher::make('Активен', 'is_active')->default(true),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        $promoId = $item->getKey();

        return [
            'code' => ['required', 'string', 'max:255', Rule::unique('promo_codes', 'code')->ignore($promoId)],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_usd' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'usage_per_user' => ['nullable', 'integer', 'min:1'],
            'active_from' => ['nullable', 'date'],
            'active_to' => ['nullable', 'date', 'after_or_equal:active_from'],
            'is_active' => ['boolean'],
        ];
    }

    private static function generateUniqueCode(): string
    {
        do {
            $code = Str::upper(Str::random(10));
        } while (PromoCode::query()->where('code', $code)->exists());

        return $code;
    }
}
