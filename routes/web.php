<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\BrandController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home');

Route::get('/brands', [BrandController::class, 'index'])
    ->name('brands.index');

Route::get('/brand/{slug}', [BrandController::class, 'show'])
    ->name('brand.show');

Route::post('/checkout', [CheckoutController::class, 'store']);
