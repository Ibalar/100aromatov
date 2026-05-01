<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\PromoCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PromoCodeCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();
    }

    public function test_valid_promo_code_applies_discount_and_creates_usage_for_customer(): void
    {
        $customer = $this->createCustomer();
        $this->actingAs($customer, 'customer');

        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        PromoCode::create([
            'code' => 'SAVE10',
            'type' => 'percent',
            'value' => 10,
            'is_active' => true,
        ]);

        $this->postJson(route('cart.add'), [
            'variant_id' => $variant->id,
            'qty' => 2,
        ])->assertOk();

        $response = $this->postJson(route('checkout.store'), $this->checkoutPayload('save10'));

        $response->assertOk()->assertJsonPath('success', true);

        $order = Order::query()->latest('id')->first();

        $this->assertNotNull($order);
        $this->assertSame('SAVE10', $order->promo_code);
        $this->assertSame('20.00', (string) $order->discount_usd);
        $this->assertDatabaseHas('promo_code_usages', [
            'order_id' => $order->id,
            'customer_id' => $customer->id,
        ]);
        $this->assertDatabaseHas('promo_codes', [
            'code' => 'SAVE10',
            'used_count' => 1,
        ]);
    }

    public function test_unknown_promo_code_returns_validation_error(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->postJson(route('cart.add'), [
            'variant_id' => $variant->id,
            'qty' => 1,
        ])->assertOk();

        $response = $this->postJson(route('checkout.store'), $this->checkoutPayload('NOPE'));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['promo_code']);
    }

    public function test_promo_code_not_yet_active_returns_validation_error(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        PromoCode::create([
            'code' => 'LATER',
            'type' => 'fixed',
            'value' => 15,
            'active_from' => now()->addDay(),
            'is_active' => true,
        ]);

        $this->postJson(route('cart.add'), [
            'variant_id' => $variant->id,
            'qty' => 1,
        ])->assertOk();

        $response = $this->postJson(route('checkout.store'), $this->checkoutPayload('LATER'));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['promo_code']);
    }

    public function test_usage_per_user_limit_is_checked_by_customer_id(): void
    {
        $customer = $this->createCustomer();
        $this->actingAs($customer, 'customer');

        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        PromoCode::create([
            'code' => 'ONCE',
            'type' => 'fixed',
            'value' => 5,
            'usage_per_user' => 1,
            'is_active' => true,
        ]);

        $this->postJson(route('cart.add'), [
            'variant_id' => $variant->id,
            'qty' => 1,
        ])->assertOk();

        $this->postJson(route('checkout.store'), $this->checkoutPayload('ONCE'))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson(route('cart.add'), [
            'variant_id' => $variant->id,
            'qty' => 1,
        ])->assertOk();

        $secondAttempt = $this->postJson(route('checkout.store'), $this->checkoutPayload('ONCE'));

        $secondAttempt->assertStatus(422);
        $secondAttempt->assertJsonValidationErrors(['promo_code']);
    }

    private function checkoutPayload(?string $promoCode = null): array
    {
        return [
            'phone' => '+375291112233',
            'call_preference' => 'call_me',
            'email' => 'buyer@example.com',
            'promo_code' => $promoCode,
            'privacy_policy' => 1,
            'website' => '',
            'form_started_at' => now()->subSeconds(3)->timestamp,
        ];
    }

    private function createCustomer(array $overrides = []): Customer
    {
        $suffix = Str::lower(Str::random(8));

        return Customer::create(array_merge([
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'phone' => '+375291110000',
            'email' => "customer_{$suffix}@example.com",
            'password' => 'secret123',
        ], $overrides));
    }

    private function createProductWithVariant(array $productOverrides = [], array $variantOverrides = []): array
    {
        $brand = $this->createBrand();
        $category = $this->createCategory();

        $nameSuffix = Str::lower(Str::random(6));
        $slug = $productOverrides['slug'] ?? ('product-' . $nameSuffix);

        $product = Product::create(array_merge([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'slug' => $slug,
            'name_ru' => 'Товар ' . $nameSuffix,
            'name_by' => 'Тавар ' . $nameSuffix,
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
            'sku' => 'SKU-' . Str::upper(Str::random(8)),
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
            'slug' => 'brand-' . $suffix,
            'name' => 'Brand ' . $suffix,
            'is_active' => true,
            'logo' => 'brands/logo.png',
        ], $overrides));
    }

    private function createCategory(array $overrides = []): Category
    {
        $suffix = Str::lower(Str::random(6));

        return Category::create(array_merge([
            'slug' => 'category-' . $suffix,
            'name_ru' => 'Категория ' . $suffix,
            'name_by' => 'Катэгорыя ' . $suffix,
            'is_active' => true,
        ], $overrides));
    }
}
