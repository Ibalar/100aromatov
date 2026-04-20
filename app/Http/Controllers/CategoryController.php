<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\FilterPage;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;

class CategoryController extends Controller
{
    public function sale(Request $request)
    {
        $rootCategories = Cache::remember('sale_root_categories', 3600, function () {
            return Category::active()
                ->whereNull('parent_id')
                ->withCount([
                    'products as products_count' => fn ($query) => $query->active(),
                ])
                ->get();
        });

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
        $brandFilter = array_values(array_filter((array) $request->input('brand', []), static fn ($value) => $value !== ''));
        $sort = $request->get('sort', 'best-selling');

        $query = Product::query()
            ->where('products.is_active', true)
            ->whereExists(function ($sub) {
                $sub->selectRaw(1)
                    ->from('product_variants as sale_variants')
                    ->whereColumn('sale_variants.product_id', 'products.id')
                    ->where('sale_variants.is_active', true)
                    ->whereNotNull('sale_variants.sale_price_usd')
                    ->where('sale_variants.sale_price_usd', '>', 0)
                    ->where('sale_variants.price_usd', '>', 0)
                    ->whereColumn('sale_variants.sale_price_usd', '<', 'sale_variants.price_usd');
            })
            ->select('products.*')
            ->selectSub(
                DB::table('product_variants as pv')
                    ->selectRaw('MIN(pv.price_usd)')
                    ->whereColumn('pv.product_id', 'products.id')
                    ->where('pv.is_active', true),
                'min_variant_price'
            )
            ->selectSub(
                DB::table('product_variants as pv')
                    ->selectRaw('MAX(pv.price_usd)')
                    ->whereColumn('pv.product_id', 'products.id')
                    ->where('pv.is_active', true),
                'max_variant_price'
            );

        if ($minPrice !== null || $maxPrice !== null) {
            $query->whereExists(function ($sub) use ($minPrice, $maxPrice) {
                $sub->selectRaw(1)
                    ->from('product_variants as price_filter_variants')
                    ->whereColumn('price_filter_variants.product_id', 'products.id')
                    ->where('price_filter_variants.is_active', true)
                    ->whereNotNull('price_filter_variants.sale_price_usd')
                    ->where('price_filter_variants.sale_price_usd', '>', 0)
                    ->where('price_filter_variants.price_usd', '>', 0)
                    ->whereColumn('price_filter_variants.sale_price_usd', '<', 'price_filter_variants.price_usd')
                    ->when(
                        $minPrice !== null && $minPrice !== '',
                        static fn ($variantQuery) => $variantQuery->where('price_filter_variants.sale_price_usd', '>=', $minPrice)
                    )
                    ->when(
                        $maxPrice !== null && $maxPrice !== '',
                        static fn ($variantQuery) => $variantQuery->where('price_filter_variants.sale_price_usd', '<=', $maxPrice)
                    );
            });
        }
        if (! empty($brandFilter)) {
            $query->whereIn('products.brand_id', $brandFilter);
        }

        if (! empty($attributeFilters)) {
            foreach ($attributeFilters as $attributeId => $values) {
                if (empty($values) || in_array('all', $values)) {
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
                'brand:id,name',
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
                'reviews' => fn ($reviewsQuery) => $reviewsQuery->where('is_approved', true),
            ])
            ->paginate(12)
            ->withQueryString();

        $filterableAttributes = Cache::remember('filterable_attributes', 3600, function () {
            return Attribute::where('is_filterable', true)
                ->with(['values' => function ($q) {
                    $q->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get();
        });

        $brands = Brand::query()
            ->select('brands.id', 'brands.name', 'brands.slug')
            ->join('products', 'products.brand_id', '=', 'brands.id')
            ->join('product_variants as sale_variants', function ($join) {
                $join->on('products.id', '=', 'sale_variants.product_id')
                    ->where('sale_variants.is_active', true)
                    ->whereNotNull('sale_variants.sale_price_usd')
                    ->where('sale_variants.sale_price_usd', '>', 0)
                    ->where('sale_variants.price_usd', '>', 0)
                    ->whereColumn('sale_variants.sale_price_usd', '<', 'sale_variants.price_usd');
            })
            ->where('brands.is_active', true)
            ->where('products.is_active', true)
            ->groupBy('brands.id', 'brands.name', 'brands.slug')
            ->selectRaw('COUNT(DISTINCT products.id) as products_count')
            ->orderBy('brands.name')
            ->get();

        $priceRange = Cache::remember('price_range_sale', 3600, function () {
            return DB::table('product_variants')
                ->join('products', 'products.id', '=', 'product_variants.product_id')
                ->where('products.is_active', true)
                ->where('product_variants.is_active', true)
                ->whereNotNull('product_variants.sale_price_usd')
                ->where('product_variants.sale_price_usd', '>', 0)
                ->where('product_variants.price_usd', '>', 0)
                ->whereColumn('product_variants.sale_price_usd', '<', 'product_variants.price_usd')
                ->selectRaw('MIN(product_variants.sale_price_usd) as min_price, MAX(product_variants.sale_price_usd) as max_price')
                ->first();
        });

        return view('categories.sale', compact(
            'products',
            'rootCategories',
            'filterableAttributes',
            'brands',
            'priceRange',
            'attributeFilters',
            'minPrice',
            'maxPrice',
            'brandFilter',
            'sort'
        ));
    }

    public function index()
    {
        $categories = Cache::remember('category_tree_active', 3600, function () {
            return Category::active()
                ->withCount([
                    'products as products_count' => fn ($query) => $query->active(),
                ])
                ->get();
        });

        $tree = $this->buildCategoryTree($categories);
        $rootCategories = collect($tree);

        $request = request();
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
        $brandFilter = array_values(array_filter((array) $request->input('brand', []), static fn ($value) => $value !== ''));
        $sort = $request->get('sort', 'best-selling');

        $query = Product::active()
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
        if (!empty($brandFilter)) {
            $query->whereIn('products.brand_id', $brandFilter);
        }

        if (!empty($attributeFilters)) {
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
                'brand' => function ($q) {
                    $q->select('id', 'name', 'slug');
                },
                'category' => function ($q) {
                    $q->select('id', 'slug', 'name_ru', 'name_by');
                },
                'variants' => function ($q) {
                    $q->select('id', 'product_id', 'volume_ml', 'price_usd', 'sale_price_usd', 'is_active')
                        ->where('is_active', true)
                        ->orderBy('price_usd');
                },
                'images' => function ($q) {
                    $q->select('id', 'product_id', 'path', 'alt_ru', 'alt_by', 'sort_order')
                        ->orderBy('sort_order')
                        ->limit(2);
                }
            ])
            ->withCount([
                'reviews' => fn ($query) => $query->where('is_approved', true),
            ])
            ->paginate(12)
            ->withQueryString();

        $filterableAttributes = Cache::remember('filterable_attributes', 3600, function () {
            return Attribute::where('is_filterable', true)
                ->with(['values' => function ($q) {
                    $q->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get();
        });

        $brands = Cache::remember('brands_with_visible_product_counts', 3600, function () {
            return Brand::active()
                ->whereHas('products', fn ($query) => $query->active())
                ->withCount([
                    'products as products_count' => fn ($query) => $query->active(),
                ])
                ->orderBy('name')
                ->get();
        });

        $priceRange = Cache::remember('price_range_catalog', 3600, function () {
            return DB::table('product_variants')
                ->join('products', 'products.id', '=', 'product_variants.product_id')
                ->where('products.is_active', true)
                ->where('product_variants.is_active', true)
                ->selectRaw('COALESCE(MIN(NULLIF(product_variants.price_usd, 0)), MIN(product_variants.price_usd)) as min_price, MAX(product_variants.price_usd) as max_price')
                ->first();
        });

        return view('categories.index', compact(
            'tree',
            'rootCategories',
            'products',
            'filterableAttributes',
            'brands',
            'attributeFilters',
            'minPrice',
            'maxPrice',
            'brandFilter',
            'sort',
            'priceRange'
        ));
    }

    public function show($slug, Request $request)
    {
        $category = Category::with(['parent', 'children' => function ($query) {
                $query->active();
            }])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

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
        $brandFilter = array_values(array_filter((array) $request->input('brand', []), static fn ($value) => $value !== ''));
        $sort = $request->get('sort', 'best-selling');

        sort($categoryIds);
        $priceRangeForInput = Cache::remember('price_range_category_' . implode('_', $categoryIds), 3600, function () use ($categoryIds) {
            return DB::table('product_variants')
                ->join('products', 'products.id', '=', 'product_variants.product_id')
                ->whereIn('products.category_id', $categoryIds)
                ->where('products.is_active', true)
                ->where('product_variants.is_active', true)
                ->selectRaw('COALESCE(MIN(NULLIF(product_variants.price_usd, 0)), MIN(product_variants.price_usd)) as min_price, MAX(product_variants.price_usd) as max_price')
                ->first();
        });

        [$minPrice, $maxPrice] = $this->normalizePriceBounds($minPrice, $maxPrice, $priceRangeForInput);

        $childCategories = $category->children;
        $sidebarCategories = $childCategories;

        if ($sidebarCategories->isEmpty()) {
            if ($category->parent) {
                $sidebarCategories = Category::where('parent_id', $category->parent_id)
                    ->active()
                    ->get();
            } else {
                $sidebarCategories = Category::whereNull('parent_id')
                    ->active()
                    ->get();
            }
        }

        // Основной запрос продуктов
        $query = Product::query()
            ->where('products.is_active', true)
            ->whereIn('products.category_id', $categoryIds)
            ->whereExists(function ($sub) {
                $sub->selectRaw(1)
                    ->from('product_variants as variants_filter')
                    ->whereColumn('variants_filter.product_id', 'products.id')
                    ->where('variants_filter.is_active', true);
            })
            ->select('products.*') // Важно: только один раз products.*
            ->selectSub(
                DB::table('product_variants as pv')
                    ->selectRaw('MIN(pv.price_usd)')
                    ->whereColumn('pv.product_id', 'products.id')
                    ->where('pv.is_active', true),
                'min_variant_price'
            )
            ->selectSub(
                DB::table('product_variants as pv')
                    ->selectRaw('MAX(pv.price_usd)')
                    ->whereColumn('pv.product_id', 'products.id')
                    ->where('pv.is_active', true),
                'max_variant_price'
            );

        // Фильтр по цене
        if ($minPrice !== null || $maxPrice !== null) {
            $query->whereExists(function ($sub) use ($minPrice, $maxPrice) {
                $sub->selectRaw(1)
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
        if (!empty($brandFilter)) {
            $query->whereIn('products.brand_id', $brandFilter);
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
        $this->applySort($query, $sort);

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
                        ->limit(2);
                }
            ])
            ->withCount([
                'reviews' => fn ($query) => $query->where('is_approved', true),
            ])
            ->paginate(12)
            ->withQueryString();

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
                ->selectRaw('COALESCE(MIN(NULLIF(product_variants.price_usd, 0)), MIN(product_variants.price_usd)) as min_price, MAX(product_variants.price_usd) as max_price')
                ->first();
        });

        $brands = Brand::query()
            ->select('brands.id', 'brands.name', 'brands.slug')
            ->join('products', 'products.brand_id', '=', 'brands.id')
            ->join('product_variants as variants_filter', function ($join) {
                $join->on('products.id', '=', 'variants_filter.product_id')
                    ->where('variants_filter.is_active', true);
            })
            ->where('brands.is_active', true)
            ->where('products.is_active', true)
            ->whereIn('products.category_id', $categoryIds)
            ->groupBy('brands.id', 'brands.name', 'brands.slug')
            ->selectRaw('COUNT(DISTINCT products.id) as products_count')
            ->orderBy('brands.name')
            ->get();

        return view('categories.show', compact(
            'category',
            'childCategories',
            'sidebarCategories',
            'products',
            'filterableAttributes',
            'brands',
            'priceRange',
            'attributeFilters',
            'minPrice',
            'maxPrice',
            'brandFilter',
            'sort'
        ));
    }

    public function showFilter($slug, $filterSlug, Request $request)
    {
        $category = Category::with(['parent', 'children' => function ($query) {
                $query->active();
            }])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $activeFilterPage = FilterPage::query()
            ->where('category_id', $category->id)
            ->where('slug', $filterSlug)
            ->firstOrFail();

        $filterData = \is_array($activeFilterPage->filter_data) ? $activeFilterPage->filter_data : [];

        $presetAttributeFilters = [];
        foreach ((array) ($filterData['attributes'] ?? []) as $row) {
            if (! \is_array($row)) {
                continue;
            }

            $attributeId = (int) ($row['attribute_id'] ?? 0);
            $valueIds = array_values(array_filter(array_map(
                static fn ($id) => (string) (int) $id,
                (array) ($row['value_ids'] ?? [])
            )));

            if ($attributeId <= 0 || $valueIds === []) {
                continue;
            }

            $presetAttributeFilters[(string) $attributeId] = $valueIds;
        }

        $categoryIds = array_unique(array_merge([$category->id], $category->getDescendantIds()));

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
        } else {
            if (($minPrice === null || $minPrice === '') && isset($filterData['min_price']) && $filterData['min_price'] !== '') {
                $minPrice = (float) $filterData['min_price'];
            }
            if (($maxPrice === null || $maxPrice === '') && isset($filterData['max_price']) && $filterData['max_price'] !== '') {
                $maxPrice = (float) $filterData['max_price'];
            }
        }

        $attributeFilters = $request->has('attributes')
            ? (array) $request->get('attributes', [])
            : $presetAttributeFilters;

        $brandFilter = $request->has('brand')
            ? array_values(array_filter((array) $request->input('brand', []), static fn ($value) => $value !== ''))
            : array_values(array_filter(array_map(
                static fn ($id) => (string) (int) $id,
                (array) ($filterData['brand'] ?? [])
            )));

        $sort = $request->get('sort', 'best-selling');

        sort($categoryIds);
        $priceRangeForInput = Cache::remember('price_range_category_' . implode('_', $categoryIds), 3600, function () use ($categoryIds) {
            return DB::table('product_variants')
                ->join('products', 'products.id', '=', 'product_variants.product_id')
                ->whereIn('products.category_id', $categoryIds)
                ->where('products.is_active', true)
                ->where('product_variants.is_active', true)
                ->selectRaw('COALESCE(MIN(NULLIF(product_variants.price_usd, 0)), MIN(product_variants.price_usd)) as min_price, MAX(product_variants.price_usd) as max_price')
                ->first();
        });

        [$minPrice, $maxPrice] = $this->normalizePriceBounds($minPrice, $maxPrice, $priceRangeForInput);

        $childCategories = $category->children;
        $sidebarCategories = $childCategories;

        if ($sidebarCategories->isEmpty()) {
            if ($category->parent) {
                $sidebarCategories = Category::where('parent_id', $category->parent_id)
                    ->active()
                    ->get();
            } else {
                $sidebarCategories = Category::whereNull('parent_id')
                    ->active()
                    ->get();
            }
        }

        $query = Product::query()
            ->where('products.is_active', true)
            ->whereIn('products.category_id', $categoryIds)
            ->whereExists(function ($sub) {
                $sub->selectRaw(1)
                    ->from('product_variants as variants_filter')
                    ->whereColumn('variants_filter.product_id', 'products.id')
                    ->where('variants_filter.is_active', true);
            })
            ->select('products.*')
            ->selectSub(
                DB::table('product_variants as pv')
                    ->selectRaw('MIN(pv.price_usd)')
                    ->whereColumn('pv.product_id', 'products.id')
                    ->where('pv.is_active', true),
                'min_variant_price'
            )
            ->selectSub(
                DB::table('product_variants as pv')
                    ->selectRaw('MAX(pv.price_usd)')
                    ->whereColumn('pv.product_id', 'products.id')
                    ->where('pv.is_active', true),
                'max_variant_price'
            );

        if ($minPrice !== null || $maxPrice !== null) {
            $query->whereExists(function ($sub) use ($minPrice, $maxPrice) {
                $sub->selectRaw(1)
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

        if (! empty($brandFilter)) {
            $query->whereIn('products.brand_id', $brandFilter);
        }

        if (! empty($attributeFilters)) {
            foreach ($attributeFilters as $attributeId => $values) {
                if (empty($values) || in_array('all', $values, true)) {
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
                'brand:id,name',
                'variants' => function ($q) {
                    $q->select('id','product_id','volume_ml','price_usd','sale_price_usd','is_active')
                        ->where('is_active', true)
                        ->orderBy('price_usd');
                },
                'images' => function ($q) {
                    $q->select('id','product_id','path','alt_ru','alt_by','sort_order')
                        ->orderBy('sort_order')
                        ->limit(2);
                }
            ])
            ->withCount([
                'reviews' => fn ($query) => $query->where('is_approved', true),
            ])
            ->paginate(12)
            ->withQueryString();

        $filterableAttributes = Cache::remember('filterable_attributes', 3600, function () {
            return Attribute::where('is_filterable', true)
                ->with(['values' => function ($q) { $q->orderBy('sort_order'); }])
                ->orderBy('sort_order')
                ->get();
        });

        sort($categoryIds);
        $cacheKey = "price_range_category_" . implode('_', $categoryIds);
        $priceRange = Cache::remember($cacheKey, 3600, function () use ($categoryIds) {
            return DB::table('product_variants')
                ->join('products', 'products.id', '=', 'product_variants.product_id')
                ->whereIn('products.category_id', $categoryIds)
                ->where('products.is_active', true)
                ->where('product_variants.is_active', true)
                ->selectRaw('COALESCE(MIN(NULLIF(product_variants.price_usd, 0)), MIN(product_variants.price_usd)) as min_price, MAX(product_variants.price_usd) as max_price')
                ->first();
        });

        $brands = Brand::query()
            ->select('brands.id', 'brands.name', 'brands.slug')
            ->join('products', 'products.brand_id', '=', 'brands.id')
            ->join('product_variants as variants_filter', function ($join) {
                $join->on('products.id', '=', 'variants_filter.product_id')
                    ->where('variants_filter.is_active', true);
            })
            ->where('brands.is_active', true)
            ->where('products.is_active', true)
            ->whereIn('products.category_id', $categoryIds)
            ->groupBy('brands.id', 'brands.name', 'brands.slug')
            ->selectRaw('COUNT(DISTINCT products.id) as products_count')
            ->orderBy('brands.name')
            ->get();

        $filterTiles = $category->filterPages()
            ->where('show_in_category_tiles', true)
            ->orderBy('h1_ru')
            ->get();

        return view('categories.show', compact(
            'category',
            'childCategories',
            'sidebarCategories',
            'activeFilterPage',
            'filterTiles',
            'products',
            'filterableAttributes',
            'brands',
            'priceRange',
            'attributeFilters',
            'minPrice',
            'maxPrice',
            'brandFilter',
            'sort'
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

    private function normalizePriceBounds($minPrice, $maxPrice, $priceRange): array
    {
        $absoluteMin = isset($priceRange->min_price) ? (float) $priceRange->min_price : null;
        $absoluteMax = isset($priceRange->max_price) ? (float) $priceRange->max_price : null;

        $normalizedMin = $minPrice !== null && $minPrice !== '' ? (float) $minPrice : null;
        $normalizedMax = $maxPrice !== null && $maxPrice !== '' ? (float) $maxPrice : null;

        $epsilon = 0.01;

        if ($normalizedMin !== null && $absoluteMin !== null && $normalizedMin <= $absoluteMin + $epsilon) {
            $normalizedMin = null;
        }

        if ($normalizedMax !== null && $absoluteMax !== null && $normalizedMax >= $absoluteMax - $epsilon) {
            $normalizedMax = null;
        }

        return [$normalizedMin, $normalizedMax];
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
                $query->orderByDesc('products.views')
                    ->orderBy('products.name_ru');
                break;
        }
    }
}
