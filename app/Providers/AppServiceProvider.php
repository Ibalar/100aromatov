<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Brand;

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

            $view->with('brandColumns', $brandColumns);

        });
    }
}
