<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home');

Route::get('/language/{lang}', [LanguageController::class, 'switch'])
    ->name('language.switch');

Route::get('/brands', [BrandController::class, 'index'])
    ->name('brands.index');

Route::get('/brand/{slug}', [BrandController::class, 'show'])
    ->name('brand.show');

// Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

Route::post('/checkout', [CheckoutController::class, 'store']);
