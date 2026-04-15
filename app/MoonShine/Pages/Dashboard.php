<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\MoonShine\Resources\Brand\BrandResource;
use App\MoonShine\Resources\Category\CategoryResource;
use App\MoonShine\Resources\Product\ProductResource;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;

#[\MoonShine\MenuManager\Attributes\SkipMenu]

class Dashboard extends Page
{
    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Дашборд';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
	{
		return [
            Box::make('Ключевые метрики', [
                Grid::make([
                    ValueMetric::make('Товары')
                        ->value(Product::query()->count())
                        ->icon('shopping-bag')
                        ->iconColor(Color::PRIMARY)
                        ->columnSpan(4, 6),
                    ValueMetric::make('Активные товары')
                        ->value(Product::query()->where('is_active', true)->count())
                        ->icon('check-circle')
                        ->iconColor(Color::SUCCESS)
                        ->columnSpan(4, 6),
                    ValueMetric::make('Категории')
                        ->value(Category::query()->count())
                        ->icon('squares-2x2')
                        ->iconColor(Color::INFO)
                        ->columnSpan(4, 6),
                    ValueMetric::make('Бренды')
                        ->value(Brand::query()->count())
                        ->icon('building-storefront')
                        ->iconColor(Color::PURPLE)
                        ->columnSpan(4, 6),
                    ValueMetric::make('Заказы за 30 дней')
                        ->value(Order::query()->where('created_at', '>=', now()->subDays(30))->count())
                        ->icon('shopping-cart')
                        ->iconColor(Color::WARNING)
                        ->columnSpan(4, 6),
                    ValueMetric::make('Выручка за 30 дней')
                        ->value(number_format(
                            (float) Order::query()
                                ->where('created_at', '>=', now()->subDays(30))
                                ->sum('total_byn'),
                            2,
                            ',',
                            ' '
                        ) . ' BYN')
                        ->icon('banknotes')
                        ->iconColor(Color::GREEN)
                        ->columnSpan(4, 6),
                ], 3),
            ]),

            Box::make('Быстрые действия', [
                Heading::make('Создание сущностей', 5),
                Flex::make([
                    ActionButton::make(
                        'Добавить товар',
                        app(ProductResource::class)->getFormPageUrl()
                    )
                        ->primary()
                        ->icon('plus')
                        ->class('btn-lg'),
                    ActionButton::make(
                        'Добавить категорию',
                        app(CategoryResource::class)->getFormPageUrl()
                    )
                        ->success()
                        ->icon('plus')
                        ->class('btn-lg'),
                    ActionButton::make(
                        'Добавить бренд',
                        app(BrandResource::class)->getFormPageUrl()
                    )
                        ->info()
                        ->icon('plus')
                        ->class('btn-lg'),
                ], justifyAlign: 'start'),
            ]),
        ];
	}
}
