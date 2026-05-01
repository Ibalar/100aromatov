<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use App\MoonShine\Resources\Category\CategoryResource;
use App\MoonShine\Resources\Brand\BrandResource;
use App\MoonShine\Resources\Product\ProductResource;
use App\MoonShine\Resources\ProductVariant\ProductVariantResource;
use App\MoonShine\Resources\ProductImage\ProductImageResource;
use App\MoonShine\Resources\Attribute\AttributeResource;
use App\MoonShine\Resources\AttributeValue\AttributeValueResource;
use App\MoonShine\Resources\FilterPage\FilterPageResource;
use App\MoonShine\Resources\Wishlist\WishlistResource;
use App\MoonShine\Resources\Setting\SettingResource;
use App\MoonShine\Resources\Order\OrderResource;
use App\MoonShine\Resources\Page\PageResource;
use App\MoonShine\Resources\Review\ReviewResource;
use App\MoonShine\Resources\Slider\SliderResource;
use App\MoonShine\Resources\PromoCode\PromoCodeResource;
use Sweet1s\MoonshineRBAC\Resource\PermissionResource;
use Sweet1s\MoonshineRBAC\Resource\RoleResource;
use Sweet1s\MoonshineRBAC\Resource\UserResource;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  CoreContract<MoonShineConfigurator>  $core
     */
    public function boot(CoreContract $core): void
    {
        $core
            ->resources([
                UserResource::class,
                RoleResource::class,
                PermissionResource::class,
                CategoryResource::class,
                BrandResource::class,
                ProductResource::class,
                ProductVariantResource::class,
                ProductImageResource::class,
                AttributeResource::class,
                AttributeValueResource::class,
                FilterPageResource::class,
                WishlistResource::class,
                SettingResource::class,
                OrderResource::class,
                PageResource::class,
                ReviewResource::class,
                SliderResource::class,
                PromoCodeResource::class,
            ])
            ->pages([
                ...$core->getConfig()->getPages(),
            ])
        ;
    }
}
