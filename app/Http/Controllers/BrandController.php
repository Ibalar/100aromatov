<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        if ($request->has('min_price_byn') || $request->has('max_price_byn')) {
            $usdRate = \App\Models\Setting::getSettings()->usd_rate ?? 1;
            if ($request->has('min_price_byn')) {
                $minPrice = $request->get('min_price_byn') / $usdRate;
            }
            if ($request->has('max_price_byn')) {
                $maxPrice = $request->get('max_price_byn') / $usdRate;
            }
        }

        $attributeFilters = $request->get('attributes', []);
        $categoryFilter = array_values(array_filter((array) $request->input('category', []), static fn ($value) => $value !== ''));

        $sort = $request->get('sort', 'a-z');

        $query = Product::query()
            ->active()
            ->where('products.brand_id', $brand->id)
            ->whereExists(function ($sub) {
                $sub->selectRaw('1')
                    ->from('product_variants as variants_filter')
                    ->whereColumn('variants_filter.product_id', 'products.id')
                    ->where('variants_filter.is_active', true);
            });

        if ($minPrice !== null || $maxPrice !== null) {
            $query->whereExists(function ($sub) use ($minPrice, $maxPrice) {
                $sub->selectRaw('1')
                    ->from('product_variants as price_filter_variants')
                    ->whereColumn('price_filter_variants.product_id', 'products.id')
                    ->where('price_filter_variants.is_active', true)
                    ->when(
                        $minPrice !== null && $minPrice !== '',
                        static fn ($variantQuery) => $variantQuery->where('price_filter_variants.price_usd', '>=', $minPrice)
                    )
                    ->when(
                        $maxPrice !== null && $maxPrice !== '',
                        static fn ($variantQuery) => $variantQuery->where('price_filter_variants.price_usd', '<=', $maxPrice)
                    );
            });
        }

        if (! empty($categoryFilter)) {
            $query->whereIn('products.category_id', $categoryFilter);
        }

        if (! empty($attributeFilters)) {
            foreach ($attributeFilters as $attributeId => $values) {
                if (empty($values) || in_array('all', $values) || in_array('', $values)) {
                    continue;
                }

                $query->whereExists(function ($sub) use ($attributeId, $values) {
                    $sub->selectRaw(1)
                        ->from('product_attribute_value')
                        ->join('attribute_values', 'attribute_values.id', '=', 'product_attribute_value.attribute_value_id')
                        ->whereColumn('product_attribute_value.product_id', 'products.id')
                        ->where('attribute_values.attribute_id', $attributeId)
                        ->whereIn('product_attribute_value.attribute_value_id', $values);
                });
            }
        }

        $this->applySort($query, $sort);

        $products = $query
            ->with([
                'brand:id,name,slug',
                'category:id,slug,name_ru,name_by',
                'variants' => function ($q) {
                    $q->select('id', 'product_id', 'volume_ml', 'price_usd', 'sale_price_usd', 'is_active')
                        ->where('is_active', true)
                        ->orderBy('price_usd');
                },
                'images' => function ($q) {
                    $q->select('id', 'product_id', 'path', 'alt_ru', 'alt_by', 'sort_order')
                        ->orderBy('sort_order')
                        ->limit(2);
                },
            ])
            ->withCount([
                'reviews' => fn ($query) => $query->where('is_approved', true),
            ])
            ->paginate(24)
            ->withQueryString();

        $filterableAttributes = Cache::remember('filterable_attributes_alpha_v1', 3600, function () {
            return Attribute::where('is_filterable', true)
                ->with(['values' => function ($q) {
                    $q->orderBy('value_ru');
                }])
                ->orderBy('name_ru')
                ->get();
        });

        $priceRange = Cache::remember("price_range_brand_{$brand->id}", 3600, function () use ($brand) {
            return DB::table('product_variants')
                ->join('products', 'products.id', '=', 'product_variants.product_id')
                ->where('products.brand_id', $brand->id)
                ->where('products.is_active', true)
                ->where('product_variants.is_active', true)
                ->selectRaw('COALESCE(MIN(NULLIF(product_variants.price_usd, 0)), MIN(product_variants.price_usd)) as min_price, MAX(product_variants.price_usd) as max_price')
                ->first();
        });

        $categories = Category::query()
            ->select('categories.id', 'categories.slug', 'categories.name_ru', 'categories.name_by')
            ->join('products', 'products.category_id', '=', 'categories.id')
            ->join('product_variants as variants_filter', function ($join) {
                $join->on('products.id', '=', 'variants_filter.product_id')
                    ->where('variants_filter.is_active', true);
            })
            ->visible()
            ->where('products.is_active', true)
            ->where('products.brand_id', $brand->id)
            ->groupBy('categories.id', 'categories.slug', 'categories.name_ru', 'categories.name_by')
            ->selectRaw('COUNT(DISTINCT products.id) as products_count')
            ->orderBy('categories.name_ru')
            ->get();

        return view('brand.show', compact(
            'brand',
            'products',
            'filterableAttributes',
            'attributeFilters',
            'categoryFilter',
            'sort',
            'priceRange',
            'minPrice',
            'maxPrice',
            'categories'
        ));
    }

    private function applySort($query, string $sort): void
    {
        $query->orderByRaw('CASE WHEN COALESCE(products.max_price, 0) <= 0 THEN 1 ELSE 0 END');

        $minPositivePriceSql = "(SELECT MIN(pv.price_usd) FROM product_variants pv WHERE pv.product_id = products.id AND pv.is_active = 1 AND pv.price_usd > 0)";
        $maxPositivePriceSql = "(SELECT MAX(pv.price_usd) FROM product_variants pv WHERE pv.product_id = products.id AND pv.is_active = 1 AND pv.price_usd > 0)";

        switch ($sort) {
            case 'a-z':
                $query->orderBy('products.name_ru');
                break;
            case 'z-a':
                $query->orderByDesc('products.name_ru');
                break;
            case 'price-low-high':
                $query->orderByRaw("COALESCE($minPositivePriceSql, products.min_price, 0)");
                break;
            case 'price-high-low':
                $query->orderByRaw("COALESCE($maxPositivePriceSql, products.max_price, 0) DESC");
                break;
            case 'best-selling':
            default:
                $query->orderByDesc('products.is_featured')
                    ->orderByDesc('products.views')
                    ->orderBy('products.name_ru');
                break;
        }
    }
}
