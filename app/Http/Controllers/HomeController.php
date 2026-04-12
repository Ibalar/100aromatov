<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::query()
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
            ->get();

        $saleProducts = Product::query()
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
            ->get();

        return view('home', compact('featuredProducts', 'saleProducts'));
    }
}
