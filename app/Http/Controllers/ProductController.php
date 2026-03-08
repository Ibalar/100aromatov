<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $attributeFilters = $request->get('attributes', []);
        $brandFilter = $request->get('brand');

        $query = Product::active()
            ->with('brand', 'category', 'variants', 'images', 'attributeValues.attribute');

        if ($minPrice) {
            $query->whereHas('variants', fn($q) => $q->where('price_usd', '>=', $minPrice));
        }
        if ($maxPrice) {
            $query->whereHas('variants', fn($q) => $q->where('price_usd', '<=', $maxPrice));
        }
        if ($brandFilter) {
            $query->where('brand_id', $brandFilter);
        }

        foreach ($attributeFilters as $attributeId => $values) {
            // Skip filtering if 'all' or empty value is selected
            if (in_array('all', $values) || in_array('', $values)) {
                continue;
            }
            $query->whereHas('attributeValues', fn($q) =>
                $q->where('attribute_id', $attributeId)->whereIn('id', $values)
            );
        }

        $products = $query->orderBy('name_ru')->paginate(24);

        $filterableAttributes = Attribute::where('is_filterable', true)
            ->with('values')
            ->orderBy('sort_order')
            ->get();

        $brands = Brand::active()->withCount('products')->orderBy('name')->get();

        return view('products.index', compact(
            'products',
            'filterableAttributes',
            'brands',
            'attributeFilters',
            'minPrice',
            'maxPrice',
            'brandFilter'
        ));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->with([
                'brand',
                'category',
                'variants',
                'images',
                'attributeValues.attribute',
                'reviews' => fn($q) => $q->where('is_approved', true)->with('user')
            ])
            ->firstOrFail();

        // Increment views
        $product->increment('views');

        return view('products.show', compact('product'));
    }
}
