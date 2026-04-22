<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;
use Intervention\Image\Interfaces\ImageManagerInterface;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Page;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Observers\ProductVariantObserver;
use App\Services\CartService;
use App\Services\LfmImageService;
use App\Services\WishlistService;
use Illuminate\Support\Facades\Auth;
use UniSharp\LaravelFilemanager\Services\ImageService as BaseLfmImageService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (interface_exists(ImageManagerInterface::class) && class_exists(BaseLfmImageService::class)) {
            $this->app->singleton(BaseLfmImageService::class, function ($app) {
                return new LfmImageService($app->make(ImageManagerInterface::class));
            });
        }

        $this->app->singleton(CartService::class);
        $this->app->singleton(WishlistService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.tf-default');
        Paginator::defaultSimpleView('vendor.pagination.tf-simple');

        // Register observer to keep product min/max prices in sync
        ProductVariant::observe(ProductVariantObserver::class);

        require_once app_path('Support/helpers.php');

        View::composer('*', function ($view) {
            static $composerData = null;

            if ($composerData === null) {
                $brands = Cache::remember('menu_brands', 3600, function () {
                    return Brand::orderBy('name')
                        ->take(39)
                        ->get();
                });

                $brandColumns = $brands->chunk(10);
                $menuCategories = Cache::remember('menu_categories', 3600, function () {
                    return Category::visible()
                        ->whereNull('parent_id')
                        ->with(['children' => function ($query) {
                            $query->visible();
                        }])
                        ->get();
                });

                $categoryColumns = $menuCategories->chunk(4);

                $menuPages = Cache::remember('menu_pages', 3600, function () {
                    return Page::query()
                        ->menu()
                        ->orderBy('sort_order')
                        ->orderBy('name_ru')
                        ->get(['id', 'slug', 'name_ru', 'name_by']);
                });

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

                $siteSettings = Setting::getSettings();

                $composerData = [
                    'brandColumns' => $brandColumns,
                    'categoryColumns' => $categoryColumns,
                    'menuPages' => $menuPages,
                    'cartCount' => $cartCount,
                    'siteSettings' => $siteSettings,
                    'customerAuth' => Auth::guard('customer')->check(),
                    'wishlistCount' => app(WishlistService::class)->count(),
                    'wishlistIds' => app(WishlistService::class)->ids(),
                ];
            }

            $view->with($composerData);
        });
    }
}
