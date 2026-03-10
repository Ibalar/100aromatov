<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Cache::remember('category_tree_active', 3600, function () {
            return Category::active()->withCount('products')->get();
        });

        $tree = $this->buildCategoryTree($categories);

        return view('categories.index', compact('tree'));
    }

    public function show($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->active()->firstOrFail();

        // Включаем текущую категорию и её потомков
        $categoryIds = array_unique(array_merge([$category->id], $category->getDescendantIds()));

        // Получаем цены из запроса
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        // Конвертация BYN в USD, если передан BYN
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

        // Основной запрос продуктов
        $query = Product::query()
            ->join('product_variants', function ($join) {
                $join->on('product_variants.product_id', '=', 'products.id')
                    ->where('product_variants.is_active', true);
            })
            ->where('products.is_active', true)
            ->whereIn('products.category_id', $categoryIds)
            ->groupBy('products.id')
            ->select('products.*') // Важно: только один раз products.*
            ->selectRaw('MIN(product_variants.price_usd) as min_variant_price, MAX(product_variants.price_usd) as max_variant_price');

        // Фильтр по цене
        if ($minPrice) {
            $query->havingRaw('MAX(product_variants.price_usd) >= ?', [$minPrice]);
        }
        if ($maxPrice) {
            $query->havingRaw('MIN(product_variants.price_usd) <= ?', [$maxPrice]);
        }

        // Фильтр по атрибутам
        if (!empty($attributeFilters)) {
            foreach ($attributeFilters as $attributeId => $values) {
                if (empty($values) || in_array('all', $values)) continue;

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

        // Подгружаем все активные варианты и изображения
        $products = $query
            ->with([
                'brand:id,name',
                'variants' => function ($q) {
                    $q->select('id','product_id','volume_ml','price_usd','sale_price_usd','is_active')
                        ->where('is_active', true)
                        ->orderBy('price_usd');
                },
                'images' => function ($q) {
                    $q->select('id','product_id','path','alt_ru','alt_by','sort_order')
                        ->orderBy('sort_order')
                        ->limit(1);
                }
            ])
            ->withCount('reviews')
            ->orderBy('products.name_ru')
            ->paginate(24);

        // Атрибуты для фильтров
        $filterableAttributes = Cache::remember('filterable_attributes', 3600, function () {
            return Attribute::where('is_filterable', true)
                ->with(['values' => function ($q) { $q->orderBy('sort_order'); }])
                ->orderBy('sort_order')
                ->get();
        });

        // Диапазон цен по категории (для слайдера фильтра)
        sort($categoryIds);
        $cacheKey = "price_range_category_" . implode('_', $categoryIds);
        $priceRange = Cache::remember($cacheKey, 3600, function () use ($categoryIds) {
            return DB::table('product_variants')
                ->join('products', 'products.id', '=', 'product_variants.product_id')
                ->whereIn('products.category_id', $categoryIds)
                ->where('products.is_active', true)
                ->where('product_variants.is_active', true)
                ->selectRaw('MIN(product_variants.price_usd) as min_price, MAX(product_variants.price_usd) as max_price')
                ->first();
        });

        return view('categories.show', compact(
            'category',
            'products',
            'filterableAttributes',
            'priceRange',
            'attributeFilters',
            'minPrice',
            'maxPrice'
        ));
    }

    private function buildCategoryTree($categories, $parentId = null)
    {
        $branch = [];

        foreach ($categories as $category) {

            if ($category->parent_id === $parentId) {

                $children = $this->buildCategoryTree($categories, $category->id);

                if ($children) {
                    $category->children = $children;
                }

                $branch[] = $category;
            }
        }

        return $branch;
    }
}
