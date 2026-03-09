<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\Brand;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Catalog with filters (similar to category show but without category filter)
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        // Convert BYN to USD if BYN inputs are provided
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
        $brandFilter = $request->get('brand');

        // Build query with JOIN instead of whereHas for better performance
        $query = Product::active()
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
        if ($brandFilter) {
            $query->where('products.brand_id', $brandFilter);
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
                    $q->select('id', 'name', 'slug');
                },
                'category' => function ($q) {
                    $q->select('id', 'slug', 'name_ru', 'name_by');
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

        // Cache filterable attributes for 1 hour
        $filterableAttributes = Cache::remember('filterable_attributes', 3600, function () {
            return Attribute::where('is_filterable', true)
                ->with(['values' => function ($q) {
                    $q->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get();
        });

        // Cache brands with product counts for 1 hour
        $brands = Cache::remember('brands_with_product_counts', 3600, function () {
            return Brand::active()->withCount('products')->orderBy('name_ru')->get();
        });

        // Calculate price range for filter
        $priceRange = Cache::remember('price_range_catalog', 3600, function () {
            return DB::table('product_variants')
                ->join('products', 'products.id', '=', 'product_variants.product_id')
                ->where('products.is_active', true)
                ->where('product_variants.is_active', true)
                ->selectRaw('MIN(product_variants.price_usd) as min_price, MAX(product_variants.price_usd) as max_price')
                ->first();
        });

        return view('products.index', compact(
            'products',
            'filterableAttributes',
            'brands',
            'attributeFilters',
            'minPrice',
            'maxPrice',
            'brandFilter',
            'priceRange'
        ));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->with([
                'brand' => function ($q) {
                    $q->select('id', 'name', 'slug');
                },
                'category' => function ($q) {
                    $q->select('id', 'slug', 'name_ru', 'name_by');
                },
                'variants' => function ($q) {
                    $q->where('is_active', true)
                        ->orderBy('price_usd');
                },
                'images' => function ($q) {
                    $q->orderBy('sort_order');
                },
                'attributeValues.attribute',
                'reviews' => function ($q) {
                    $q->where('is_approved', true)
                        ->with('user:id,name')
                        ->latest();
                }
            ])
            ->withCount('reviews')
            ->firstOrFail();

        // Increment views
        $product->increment('views');

        return view('products.show', compact('product'));
    }
}
