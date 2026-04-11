<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Brand::query()
            ->where('is_active', true);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $brands = $query
            ->orderBy('name')
            ->get();

        // Группировка по первой букве
        $grouped = $brands->groupBy(function ($brand) {
            $letter = mb_strtoupper(mb_substr($brand->name, 0, 1));

            // латиница — отдельные буквы
            if (preg_match('/^[A-Z]$/', $letter)) {
                return $letter;
            }

            // кириллица — одна группа
            if (preg_match('/^[А-ЯЁ]$/u', $letter)) {
                return 'А-Я';
            }

            // цифры и прочее
            return '0-9';
        })->sortKeys();

        // Список всех букв (для меню навигации)
        $letters = $grouped->keys();

        return view('brands.index', compact('brands', 'grouped', 'letters', 'search'));
    }

    public function show($slug, Request $request)
    {
        $brand = Brand::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = $brand->products()
            ->active()
            ->with('brand', 'variants', 'images')
            ->withCount([
                'reviews' => fn ($query) => $query->where('is_approved', true),
            ]);

        $sort = $request->get('sort', 'best-selling');

        $query->orderByRaw('CASE WHEN COALESCE(max_price, 0) <= 0 THEN 1 ELSE 0 END');

        $minPositivePriceSql = "(SELECT MIN(pv.price_usd) FROM product_variants pv WHERE pv.product_id = products.id AND pv.is_active = 1 AND pv.price_usd > 0)";
        $maxPositivePriceSql = "(SELECT MAX(pv.price_usd) FROM product_variants pv WHERE pv.product_id = products.id AND pv.is_active = 1 AND pv.price_usd > 0)";

        switch ($sort) {
            case 'a-z':
                $query->orderBy('name_ru');
                break;
            case 'z-a':
                $query->orderByDesc('name_ru');
                break;
            case 'price-low-high':
                $query->orderByRaw("COALESCE($minPositivePriceSql, min_price, 0)");
                break;
            case 'price-high-low':
                $query->orderByRaw("COALESCE($maxPositivePriceSql, max_price, 0) DESC");
                break;
            case 'best-selling':
            default:
                $query->orderByDesc('is_featured')
                    ->orderByDesc('views')
                    ->orderBy('name_ru');
                break;
        }

        $products = $query
            ->paginate(24);

        return view('brand.show', compact('brand', 'products'));
    }
}
