<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutSuccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_success_page_shows_order_details(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $response = $this->post(route('checkout.store'), [
            'phone' => '+375291112233',
            'call_preference' => 'call_me',
            'email' => 'buyer@example.com',
            'privacy_policy' => 1,
            'website' => '',
            'form_started_at' => now()->subSeconds(3)->timestamp,
        ]);

        $order = Order::first();

        $response->assertRedirect(route('checkout.success', $order));

        $successResponse = $this->get(route('checkout.success', $order));
        $successResponse->assertOk();
        $successResponse->assertSee((string) $order->id);
        $successResponse->assertSee('Заказ');
    }

    public function test_success_page_shows_order_total(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $this->post(route('checkout.store'), [
            'phone' => '+375291112233',
            'call_preference' => 'call_me',
            'privacy_policy' => 1,
            'website' => '',
            'form_started_at' => now()->subSeconds(3)->timestamp,
        ]);

        $order = Order::first();

        $response = $this->get(route('checkout.success', $order));
        $response->assertOk();
        $response->assertSee('Итого');
    }

    public function test_success_page_has_return_to_shop_link(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $this->post(route('checkout.store'), [
            'phone' => '+375291112233',
            'call_preference' => 'call_me',
            'privacy_policy' => 1,
            'website' => '',
            'form_started_at' => now()->subSeconds(3)->timestamp,
        ]);

        $order = Order::first();

        $response = $this->get(route('checkout.success', $order));
        $response->assertOk();
        $response->assertSee(route('home'));
    }

    public function test_success_page_redirects_if_not_owner(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $this->post(route('checkout.store'), [
            'phone' => '+375291112233',
            'call_preference' => 'call_me',
            'privacy_policy' => 1,
            'website' => '',
            'form_started_at' => now()->subSeconds(3)->timestamp,
        ]);

        $order = Order::first();
        $anotherOrder = Order::create([
            'status' => 'new',
            'total_usd' => 50,
            'total_byn' => 150,
            'phone' => '+375291110000',
        ]);

        $response = $this->get(route('checkout.success', $anotherOrder));
        $response->assertRedirect(route('home'));
    }

    private function addToCart(ProductVariant $variant): void
    {
        $this->post(route('cart.add'), [
            'variant_id' => $variant->id,
            'qty' => 1,
        ]);
    }

    private function createProductWithVariant(array $productOverrides = [], array $variantOverrides = []): array
    {
        $brand = $this->createBrand();
        $category = $this->createCategory();

        $nameSuffix = Str::lower(Str::random(6));
        $slug = $productOverrides['slug'] ?? ('product-'.$nameSuffix);

        $product = Product::create(array_merge([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'slug' => $slug,
            'name_ru' => 'Товар '.$nameSuffix,
            'name_by' => 'Тавар '.$nameSuffix,
            'description_ru' => 'Описание',
            'description_by' => 'Апісанне',
            'country' => 'France',
            'country_by' => 'France',
            'gender' => 'unisex',
            'gender_by' => 'unisex',
            'concentration' => 'EDP',
            'concentration_by' => 'EDP',
            'is_active' => true,
            'is_featured' => false,
            'views' => 0,
        ], $productOverrides));

        $variant = ProductVariant::create(array_merge([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::upper(Str::random(8)),
            'volume_ml' => '50',
            'price_usd' => 100,
            'sale_price_usd' => null,
            'is_active' => true,
        ], $variantOverrides));

        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/test.jpg',
            'sort_order' => 0,
            'alt_ru' => $product->name_ru,
            'alt_by' => $product->name_by,
        ]);

        return [$product, $variant];
    }

    private function createBrand(array $overrides = []): Brand
    {
        $suffix = Str::lower(Str::random(6));

        return Brand::create(array_merge([
            'slug' => 'brand-'.$suffix,
            'name' => 'Brand '.$suffix,
            'is_active' => true,
            'logo' => 'brands/logo.png',
        ], $overrides));
    }

    private function createCategory(array $overrides = []): Category
    {
        $suffix = Str::lower(Str::random(6));

        return Category::create(array_merge([
            'slug' => 'category-'.$suffix,
            'name_ru' => 'Категория '.$suffix,
            'name_by' => 'Катэгорыя '.$suffix,
            'is_active' => true,
        ], $overrides));
    }
}
