<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductAvailabilityInquiryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\AdminFeedExportController;
use Illuminate\Support\Facades\Route;
use MoonShine\Laravel\Http\Middleware\Authenticate as MoonShineAuthenticate;
use UniSharp\LaravelFilemanager\Lfm;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.xml');
Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');

Route::get('/language/{lang}', [LanguageController::class, 'switch'])
    ->name('language.switch');

Route::get('/brands', [BrandController::class, 'index'])
    ->name('brands.index');
Route::get('/sale', [CategoryController::class, 'sale'])
    ->name('sale.index');

Route::get('/brand/{slug}', [BrandController::class, 'show'])
    ->name('brand.show');

// Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/category/{slug}/filter/{filterSlug}', [CategoryController::class, 'showFilter'])->name('category.filter');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

// Products
Route::get('/search', [ProductController::class, 'search'])->name('search');
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::get('/product/{product}/quick-view', [ProductController::class, 'quickView'])
    ->whereNumber('product')
    ->name('product.quick-view');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/product/{slug}/reviews', [ReviewController::class, 'store'])
    ->middleware('auth:customer')
    ->middleware('throttle:3,10')
    ->name('product.reviews.store');
Route::post('/reviews', [ReviewController::class, 'storeStore'])
    ->middleware('auth:customer')
    ->middleware('throttle:3,10')
    ->name('reviews.store');
Route::post('/product-availability-inquiry', [ProductAvailabilityInquiryController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('product.availability-inquiry.store');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::prefix('/cart')->name('cart.')->group(function () {
    Route::get('/summary', [CartController::class, 'summary'])->name('summary');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('checkout.store');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::prefix('/wishlist')->name('wishlist.')->group(function () {
    Route::get('/summary', [WishlistController::class, 'summary'])->name('summary');
    Route::post('/toggle', [WishlistController::class, 'toggle'])->name('toggle');
    Route::post('/clear', [WishlistController::class, 'clear'])->name('clear');
});

Route::middleware('guest:customer')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
    Route::post('/login', [CustomerAuthController::class, 'login'])
        ->middleware('throttle:5,10')
        ->name('customer.login.store');
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
    Route::post('/register', [CustomerAuthController::class, 'register'])
        ->middleware('throttle:3,10')
        ->name('customer.register.store');
});

Route::middleware('auth:customer')->group(function () {
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');

    Route::prefix('/account')->name('customer.account.')->group(function () {
        Route::get('/', [CustomerAccountController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [CustomerAccountController::class, 'orders'])->name('orders');
        Route::get('/profile', [CustomerAccountController::class, 'profile'])->name('profile');
        Route::post('/profile', [CustomerAccountController::class, 'updateProfile'])->name('profile.update');
        Route::get('/security', [CustomerAccountController::class, 'security'])->name('security');
        Route::post('/security', [CustomerAccountController::class, 'updatePassword'])->name('security.update');
        Route::get('/addresses', [CustomerAccountController::class, 'addresses'])->name('addresses');
    });
});

Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

$moonshinePrefix = trim((string) env('MOONSHINE_ROUTE_PREFIX', 'admin'), '/');
$lfmPrefix = $moonshinePrefix !== ''
    ? $moonshinePrefix . '/laravel-filemanager'
    : 'laravel-filemanager';

Route::middleware(['moonshine', MoonShineAuthenticate::class])
    ->prefix($lfmPrefix)
    ->group(function (): void {
        Lfm::routes();
    });

Route::prefix($moonshinePrefix)->group(function () use ($moonshinePrefix): void {
    $base = '/' . trim($moonshinePrefix, '/');

    Route::redirect(
        '/resource/moon-shine-user-resource/moon-shine-user-index-page',
        $base . '/resource/user-resource/index-page',
        301
    );
    Route::redirect(
        '/resource/moon-shine-user-role-resource/moon-shine-user-role-index-page',
        $base . '/resource/role-resource/index-page',
        301
    );
    Route::redirect(
        '/resource/moon-shine-user-resource/moon-shine-user-form-page/{resourceItem}',
        $base . '/resource/user-resource/form-page/{resourceItem}',
        301
    );
    Route::redirect(
        '/resource/moon-shine-user-role-resource/moon-shine-user-role-form-page/{resourceItem}',
        $base . '/resource/role-resource/form-page/{resourceItem}',
        301
    );

    Route::middleware(['moonshine', MoonShineAuthenticate::class])->group(function (): void {
        Route::get('/feed-profiles/{profile}/download', [AdminFeedExportController::class, 'download'])
            ->name('admin.feed-profiles.download');
    });
});

Route::get('/{path}', [ProductController::class, 'redirectByOldUrl'])
    ->where('path', '.*')
    ->name('product.redirect.old');
