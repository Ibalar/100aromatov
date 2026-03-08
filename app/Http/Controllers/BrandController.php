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

        return view('brands.index', compact('grouped', 'letters', 'search'));
    }

    public function show($slug)
    {
        $brand = Brand::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = $brand->products()
            ->active()
            ->with('brand', 'variants', 'images')
            ->orderBy('name_ru')
            ->paginate(24);

        return view('brand.show', compact('brand', 'products'));
    }
}
