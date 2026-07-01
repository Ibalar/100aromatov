<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $slides = Cache::remember('home_slides', 3600, fn () => Slider::query()
            ->where('is_active', true)
            ->slides()
            ->orderBy('sort_order')
            ->get());

        $banners = Cache::remember('home_banners', 3600, fn () => Slider::query()
            ->where('is_active', true)
            ->banners()
            ->orderBy('sort_order')
            ->get());

        $featuredProducts = Cache::remember('home_featured_products', 600, fn () => Product::query()
            ->active()
            ->where('is_featured', true)
            ->whereHas('variants', fn ($query) => $query->where('is_active', true))
            ->with([
                'variants' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('price_usd'),
                'images' => fn ($query) => $query
                    ->orderBy('sort_order')
                    ->limit(2),
            ])
            ->orderByDesc('id')
            ->limit(8)
            ->get());

        $saleProducts = Cache::remember('home_sale_products', 600, fn () => Product::query()
            ->active()
            ->whereHas('variants', function ($query) {
                $query->where('is_active', true)
                    ->whereNotNull('sale_price_usd')
                    ->where('sale_price_usd', '>', 0);
            })
            ->with([
                'variants' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('price_usd'),
                'images' => fn ($query) => $query
                    ->orderBy('sort_order')
                    ->limit(2),
            ])
            ->orderByDesc('id')
            ->limit(8)
            ->get());

        $homeBrands = Cache::remember('home_brands', 3600, fn () => Brand::query()
            ->active()
            ->whereNotNull('logo')
            ->where('logo', '!=', '')
            ->inRandomOrder()
            ->limit(10)
            ->get(['id', 'slug', 'name', 'logo']));

        return view('home', compact('slides', 'banners', 'featuredProducts', 'saleProducts', 'homeBrands'));
    }
}
