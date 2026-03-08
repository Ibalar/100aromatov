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
        // Cache category tree with product counts for 1 hour
        $categories = Cache::remember('category_tree_active', 3600, function () {
            return Category::active()->withCount('products')->get();
        });

        $tree = $this->buildCategoryTree($categories);
        return view('categories.index', compact('tree'));
    }

    public function show($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->active()->firstOrFail();

        // Get all category IDs including descendants
        $categoryIds = $category->getDescendantIds();

        // Price filter
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        // Attribute filters (is_filterable attributes only)
        $attributeFilters = $request->get('attributes', []);

        // Build query with JOIN instead of whereHas for better performance
        $query = Product::active()
            ->whereIn('category_id', $categoryIds)
            ->join('product_variants as variants_filter', 'products.id', '=', 'variants_filter.product_id')
            ->where('variants_filter.is_active', true)
            ->select('products.*')
            ->distinct();

        // Price filtering using stored min/max columns (instant, no JOIN needed)
        if ($minPrice) {
            $query->where('products.min_price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('products.max_price', '<=', $maxPrice);
        }

        // Attribute filtering with optimized whereExists
        if (!empty($attributeFilters)) {
            $query->whereExists(function ($sub) use ($attributeFilters) {
                $sub->selectRaw('1')
                    ->from('product_attribute_value')
                    ->whereColumn('product_attribute_value.product_id', 'products.id')
                    ->where(function ($subSub) use ($attributeFilters) {
                        $first = true;
                        foreach ($attributeFilters as $attributeId => $values) {
                            // Skip filtering if 'all' or empty value is selected
                            if (in_array('all', $values) || in_array('', $values)) {
                                continue;
                            }
                            if (!empty($values)) {
                                if ($first) {
                                    $subSub->where(function ($or) use ($attributeId, $values) {
                                        $or->where('attribute_id', $attributeId)
                                           ->whereIn('attribute_value_id', $values);
                                    });
                                    $first = false;
                                } else {
                                    $subSub->orWhere(function ($or) use ($attributeId, $values) {
                                        $or->where('attribute_id', $attributeId)
                                           ->whereIn('attribute_value_id', $values);
                                    });
                                }
                            }
                        }
                    });
            });
        }

        // Use simplePaginate to eliminate COUNT query (2-3x faster)
        $products = $query
            ->with([
                // Limited eager loading - select only needed columns
                'brand' => function ($q) {
                    $q->select('id', 'name_ru', 'name_by');
                },
                'variants' => function ($q) {
                    $q->select('id', 'product_id', 'volume_ml', 'price_usd', 'sale_price_usd', 'is_active')
                        ->where('is_active', true)
                        ->orderBy('price_usd')
                        ->limit(1); // Only min price variant
                },
                'images' => function ($q) {
                    $q->select('id', 'product_id', 'path', 'alt_ru', 'alt_by', 'sort_order')
                        ->orderBy('sort_order')
                        ->limit(1); // Only first image
                }
            ])
            ->withCount('reviews') // Use aggregate instead of loading all reviews
            ->orderBy('products.name_ru')
            ->simplePaginate(24);

        // Cache filterable attributes for 1 hour (eliminates 1 heavy query per page)
        $filterableAttributes = Cache::remember('filterable_attributes', 3600, function () {
            return Attribute::where('is_filterable', true)
                ->with(['values' => function ($q) {
                    $q->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get();
        });

        // Cache price range for this category tree for 1 hour (eliminates MIN/MAX scan)
        $cacheKey = "price_range_category_" . md5(implode(',', $categoryIds));
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
