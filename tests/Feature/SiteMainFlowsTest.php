<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class SiteMainFlowsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_home_page_is_available_with_featured_and_sale_products(): void
    {
        [$product] = $this->createProductWithVariant([
            'is_featured' => true,
            'name_ru' => 'Тестовый товар для главной',
        ], [
            'price_usd' => 100,
            'sale_price_usd' => 80,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewIs('home');
        $response->assertSee($product->name_ru);
    }

    public function test_catalog_page_is_available(): void
    {
        [$product] = $this->createProductWithVariant([
            'name_ru' => 'Товар каталога',
        ]);

        $response = $this->get(route('categories.index'));

        $response->assertOk();
        $response->assertViewIs('categories.index');
        $response->assertSee($product->name_ru);
    }

    public function test_category_page_is_available_by_slug(): void
    {
        $category = $this->createCategory([
            'slug' => 'aromaty',
            'name_ru' => 'Ароматы',
            'name_by' => 'Араматы',
        ]);

        [$product] = $this->createProductWithVariant([
            'category_id' => $category->id,
            'name_ru' => 'Товар в категории',
        ]);

        $response = $this->get(route('category.show', $category->slug));

        $response->assertOk();
        $response->assertViewIs('categories.show');
        $response->assertSee($product->name_ru);
    }

    public function test_product_page_increments_views(): void
    {
        [$product] = $this->createProductWithVariant([
            'slug' => 'test-product',
            'views' => 0,
        ]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertOk();
        $response->assertViewIs('products.show');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'views' => 1,
        ]);
    }

    public function test_search_returns_matching_product(): void
    {
        [$product, $variant] = $this->createProductWithVariant([
            'name_ru' => 'Поисковый товар',
        ], [
            'sku' => 'SEARCH-001',
        ]);

        $response = $this->get(route('search', ['q' => $variant->sku]));

        $response->assertOk();
        $response->assertViewIs('products.search');
        $response->assertSee($product->name_ru);
    }

    public function test_contacts_page_uses_settings_email(): void
    {
        Setting::getSettings()->update([
            'email' => 'info@example.com',
        ]);

        $response = $this->get(route('contacts.index'));

        $response->assertOk();
        $response->assertViewIs('contact');
        $response->assertSee('info@example.com');
    }

    public function test_cart_main_endpoints_work(): void
    {
        [, $variant] = $this->createProductWithVariant();

        $addResponse = $this->postJson(route('cart.add'), [
            'variant_id' => $variant->id,
            'qty' => 2,
        ]);

        $addResponse->assertOk()->assertJsonPath('success', true);

        $summaryResponse = $this->getJson(route('cart.summary'));
        $summaryResponse->assertOk();
        $summaryResponse->assertJsonPath('count', 2);

        $updateResponse = $this->postJson(route('cart.update'), [
            'variant_id' => $variant->id,
            'qty' => 3,
        ]);

        $updateResponse->assertOk()->assertJsonPath('success', true);
        $this->getJson(route('cart.summary'))->assertJsonPath('count', 3);

        $removeResponse = $this->postJson(route('cart.remove'), [
            'variant_id' => $variant->id,
        ]);

        $removeResponse->assertOk()->assertJsonPath('success', true);
        $this->getJson(route('cart.summary'))->assertJsonPath('count', 0);
    }

    public function test_checkout_creates_order_from_cart(): void
    {
        [, $variant] = $this->createProductWithVariant([], [
            'price_usd' => 50,
        ]);

        $this->postJson(route('cart.add'), [
            'variant_id' => $variant->id,
            'qty' => 2,
        ])->assertOk();

        $response = $this->postJson(route('checkout.store'), [
            'phone' => '+375291112233',
            'email' => 'buyer@example.com',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure(['success', 'order_id']);

        $order = Order::query()->latest('id')->first();

        $this->assertNotNull($order);
        $this->assertSame('buyer@example.com', $order->email);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'sku_snapshot' => $variant->sku,
            'qty' => 2,
        ]);

        $this->assertSame([], session('cart.items', []));
    }

    public function test_active_page_is_accessible_and_inactive_page_returns_404(): void
    {
        $activePage = Page::create([
            'slug' => 'delivery',
            'name_ru' => 'Доставка',
            'name_by' => 'Дастаўка',
            'description_ru' => 'Описание доставки',
            'description_by' => 'Апісанне дастаўкі',
            'is_active' => true,
            'show_in_menu' => true,
        ]);

        Page::create([
            'slug' => 'hidden-page',
            'name_ru' => 'Скрытая',
            'name_by' => 'Схаваная',
            'is_active' => false,
            'show_in_menu' => false,
        ]);

        $this->get(route('pages.show', $activePage->slug))
            ->assertOk()
            ->assertViewIs('pages.show')
            ->assertSee($activePage->name_ru);

        $this->get(route('pages.show', 'hidden-page'))->assertNotFound();
    }

    public function test_old_product_url_redirects_to_new_slug(): void
    {
        [$product] = $this->createProductWithVariant([
            'slug' => 'new-product-slug',
            'old_url' => '/legacy/product-slug',
        ]);

        $response = $this->get('/legacy/product-slug');

        $response->assertRedirect(route('product.show', $product->slug));
        $response->assertStatus(301);
    }

    private function createProductWithVariant(array $productOverrides = [], array $variantOverrides = []): array
    {
        $brand = isset($productOverrides['brand_id'])
            ? Brand::findOrFail($productOverrides['brand_id'])
            : $this->createBrand();

        $category = isset($productOverrides['category_id'])
            ? Category::findOrFail($productOverrides['category_id'])
            : $this->createCategory();

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
