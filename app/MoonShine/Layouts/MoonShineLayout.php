<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Resources\Order\OrderResource;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\Review\ReviewResource;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;
use App\MoonShine\Resources\Category\CategoryResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use App\MoonShine\Resources\Brand\BrandResource;
use App\MoonShine\Resources\Product\ProductResource;
use App\MoonShine\Resources\ProductVariant\ProductVariantResource;
use App\MoonShine\Resources\ProductImage\ProductImageResource;
use App\MoonShine\Resources\Attribute\AttributeResource;
use App\MoonShine\Resources\AttributeValue\AttributeValueResource;
use App\MoonShine\Resources\FilterPage\FilterPageResource;
use App\MoonShine\Resources\Wishlist\WishlistResource;
use App\MoonShine\Resources\Setting\SettingResource;

final class MoonShineLayout extends AppLayout
{
    /**
     * @var null|class-string<PaletteContract>
     */
    protected ?string $palette = PurplePalette::class;


    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function menu(): array
    {
        return [
            MenuGroup::make('Каталог', [
                MenuItem::make(ProductResource::class, 'Товары'),
                MenuItem::make(ProductVariantResource::class, 'Варианты товаров'),
                MenuItem::make(ProductImageResource::class, 'Изображения товаров'),
            ])->icon('shopping-bag'),
            MenuItem::make(CategoryResource::class, 'Категории')->icon('document-duplicate'),
            MenuItem::make(BrandResource::class, 'Бренды')->icon('rectangle-group'),
            MenuGroup::make('Характеристики', [
                MenuItem::make(AttributeResource::class, 'Характеристики'),
                MenuItem::make(AttributeValueResource::class, 'Значения характеристик'),
                MenuItem::make(FilterPageResource::class, 'Страницы для фильтра'),
            ])->icon('adjustments-vertical'),
            MenuItem::make(PageResource::class, 'Статические страницы')->icon('computer-desktop'),
            MenuGroup::make('Заказы и статистика', [
                MenuItem::make(OrderResource::class, 'Заказы'),
                MenuItem::make(WishlistResource::class, 'Списки избранного'),
            ])->icon('shopping-cart'),
            MenuItem::make(ReviewResource::class, 'Отзывы')->icon('chat-bubble-left-right'),
            MenuItem::make(SettingResource::class, 'Настройки сайта')->icon('cog-8-tooth'),
            ...parent::menu(),
        ];
    }

    protected function getFooterMenu(): array
    {
        return [
            url('/wiki/') => 'Wiki для администраторов',
        ];
    }

    protected function getFooterCopyright(): string
    {
        return 'Веб-панель сайта - WebArt.BY';
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }
}
