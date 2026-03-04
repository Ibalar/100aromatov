<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home');

Route::post('/checkout', [CheckoutController::class, 'store']);
