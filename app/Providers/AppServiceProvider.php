<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Brand;
use App\Models\ProductVariant;
use App\Observers\ProductVariantObserver;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observer to keep product min/max prices in sync
        ProductVariant::observe(ProductVariantObserver::class);

        require_once app_path('Support/helpers.php');

        View::composer('*', function ($view) {

            $brands = Cache::remember('menu_brands', 3600, function () {
                return Brand::orderBy('name')
                    ->take(39)
                    ->get();
            });

            $brandColumns = $brands->chunk(10);


            $brandColumns = $brandColumns->map(function ($column, $index) use ($brandColumns) {
                if ($index === $brandColumns->count() - 1) {
                    return $column->take(9);
                }
                return $column;
            });

            $cartCount = 0;
            if (app()->bound('request') && request()->hasSession()) {
                $cartCount = app(CartService::class)->getSummary()['total_qty'];
            }

            $view->with('brandColumns', $brandColumns);
            $view->with('cartCount', $cartCount);
            $view->with('customerAuth', Auth::guard('customer')->check());
            $view->with('wishlistCount', app(WishlistService::class)->count());
            $view->with('wishlistIds', app(WishlistService::class)->ids());

        });
    }
}
