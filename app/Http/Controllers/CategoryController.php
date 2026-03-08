<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()->withCount('products')->get();
        $tree = $this->buildCategoryTree($categories);
        return view('categories.index', compact('tree'));
    }

    public function show($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->active()->firstOrFail();

        // Price filter
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        // Attribute filters (is_filterable attributes only)
        $attributeFilters = $request->get('attributes', []);

        $query = Product::active()
            ->where('category_id', $category->id)
            ->with('brand', 'variants', 'images', 'attributeValues.attribute')
            ->whereHas('variants', fn($q) => $q->where('is_active', true));

        if ($minPrice) {
            $query->whereHas('variants', fn($q) => $q->where('price_usd', '>=', $minPrice));
        }
        if ($maxPrice) {
            $query->whereHas('variants', fn($q) => $q->where('price_usd', '<=', $maxPrice));
        }

        foreach ($attributeFilters as $attributeId => $values) {
            $query->whereHas('attributeValues', fn($q) =>
                $q->where('attribute_id', $attributeId)->whereIn('id', $values)
            );
        }

        $products = $query->orderBy('name_ru')->paginate(24);

        // Get filterable attributes for this category
        $filterableAttributes = Attribute::where('is_filterable', true)
            ->with('values')
            ->orderBy('sort_order')
            ->get();

        // Get min/max price for products in category
        $priceRange = Product::active()
            ->where('category_id', $category->id)
            ->whereHas('variants')
            ->selectRaw('MIN(variants.price_usd) as min_price, MAX(variants.price_usd) as max_price')
            ->join('product_variants as variants', 'products.id', '=', 'variants.product_id')
            ->first();

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
