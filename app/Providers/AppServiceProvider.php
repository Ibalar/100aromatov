<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
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
        // Share locale with all views
        $locale = Session::get('locale', 'ru');
        view()->share('locale', $locale);
        view()->share('currentLocale', $locale);

        View::composer('*', function ($view) use ($locale) {

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

/**
 * Get localized field value from model
 * 
 * @param mixed $model The model instance
 * @param string $field The field name without locale suffix
 * @return string The localized field value
 */
function localizedField($model, string $field): ?string
{
    $locale = Session::get('locale', 'ru');
    
    $localizedField = $field . '_' . $locale;
    
    // Check if the localized field exists and has a value
    if (isset($model->{$localizedField}) && !empty($model->{$localizedField})) {
        return $model->{$localizedField};
    }
    
    // Fallback to Russian if Belarusian is empty
    if ($locale === 'by') {
        $fallbackField = $field . '_ru';
        if (isset($model->{$fallbackField}) && !empty($model->{$fallbackField})) {
            return $model->{$fallbackField};
        }
    }
    
    // Fallback to Belarusian if Russian is empty
    if ($locale === 'ru') {
        $fallbackField = $field . '_by';
        if (isset($model->{$fallbackField}) && !empty($model->{$fallbackField})) {
            return $model->{$fallbackField};
        }
    }
    
    return null;
}
