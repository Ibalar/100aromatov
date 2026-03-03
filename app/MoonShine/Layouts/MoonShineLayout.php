<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

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
            MenuItem::make(AttributeResource::class, 'Характеристики'),
            MenuItem::make(AttributeValueResource::class, 'Значения характеристик'),
            MenuItem::make(FilterPageResource::class, 'Страницы для фильтра'),
            MenuItem::make(WishlistResource::class, 'Избранное'),
            ...parent::menu(),
        ];
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
