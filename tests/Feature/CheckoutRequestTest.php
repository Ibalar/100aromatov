<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_checkout_requires_phone(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $payload = $this->checkoutPayload();
        unset($payload['phone']);

        $this->post(route('checkout.store'), $payload)
            ->assertSessionHasErrors('phone');
    }

    public function test_checkout_rejects_invalid_belarus_phone(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $payload = $this->checkoutPayload();
        $payload['phone'] = '+1234567890';

        $this->post(route('checkout.store'), $payload)
            ->assertSessionHasErrors('phone');
    }

    public function test_checkout_validates_belarus_phone_format(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $payload = $this->checkoutPayload();
        $payload['phone'] = '+375291112233';

        $response = $this->post(route('checkout.store'), $payload);

        $response->assertSessionDoesntHaveErrors('phone');
    }

    public function test_checkout_requires_privacy_policy_acceptance(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $payload = $this->checkoutPayload();
        unset($payload['privacy_policy']);

        $this->post(route('checkout.store'), $payload)
            ->assertSessionHasErrors('privacy_policy');
    }

    public function test_checkout_requires_call_preference(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $payload = $this->checkoutPayload();
        unset($payload['call_preference']);

        $this->post(route('checkout.store'), $payload)
            ->assertSessionHasErrors('call_preference');
    }

    public function test_checkout_rejects_invalid_call_preference(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $payload = $this->checkoutPayload();
        $payload['call_preference'] = 'invalid_option';

        $this->post(route('checkout.store'), $payload)
            ->assertSessionHasErrors('call_preference');
    }

    public function test_checkout_validates_email_format(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $payload = $this->checkoutPayload();
        $payload['email'] = 'not-an-email';

        $this->post(route('checkout.store'), $payload)
            ->assertSessionHasErrors('email');
    }

    public function test_checkout_allows_null_email(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $payload = $this->checkoutPayload();
        $payload['email'] = null;

        $response = $this->post(route('checkout.store'), $payload);

        $response->assertSessionDoesntHaveErrors('email');
    }

    public function test_checkout_rejects_honeypot_filled(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $payload = $this->checkoutPayload();
        $payload['website'] = 'spam';

        $this->post(route('checkout.store'), $payload)
            ->assertSessionHasErrors('website');
    }

    public function test_checkout_rejects_too_fast_submission(): void
    {
        [, $variant] = $this->createProductWithVariant([], ['price_usd' => 100]);

        $this->addToCart($variant);

        $payload = $this->checkoutPayload();
        $payload['form_started_at'] = now()->subSecond()->timestamp;

        $this->post(route('checkout.store'), $payload)
            ->assertSessionHasErrors('phone');
    }

    public function test_checkout_requires_items_from_cart(): void
    {
        $payload = $this->checkoutPayload();

        $this->post(route('checkout.store'), $payload)
            ->assertSessionHasErrors('cart');
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
