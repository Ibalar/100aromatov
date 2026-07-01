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

class RecentlyViewedTest extends TestCase
{
    use RefreshDatabase;

    public function test_viewing_product_sets_cookie(): void
    {
        $product = $this->createProduct();

        $response = $this->get(route('product.show', $product->slug));

        $response->assertOk();
        $response->assertCookie('recently_viewed');
    }

    public function test_cookie_contains_viewed_product_id(): void
    {
        $product = $this->createProduct();

        $this->get(route('product.show', $product->slug));

        $cookie = $this->getCookieValue('recently_viewed');

        $this->assertIsArray($cookie);
        $this->assertContains($product->id, $cookie);
    }

    public function test_new_product_is_first_in_cookie(): void
    {
        $productA = $this->createProduct(['slug' => 'product-a']);
        $productB = $this->createProduct(['slug' => 'product-b']);

        $this->get(route('product.show', $productA->slug));
        $this->get(route('product.show', $productB->slug));

        $cookie = $this->getCookieValue('recently_viewed');

        $this->assertEquals($productB->id, $cookie[0]);
        $this->assertEquals($productA->id, $cookie[1]);
    }

    public function test_duplicates_are_removed(): void
    {
        $product = $this->createProduct();

        $this->get(route('product.show', $product->slug));
        $this->get(route('product.show', $product->slug));
        $this->get(route('product.show', $product->slug));

        $cookie = $this->getCookieValue('recently_viewed');

        $this->assertCount(1, $cookie);
    }

    public function test_cookie_limited_to_12_products(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $product = $this->createProduct(['slug' => 'product-'.$i]);
            $this->get(route('product.show', $product->slug));
        }

        $cookie = $this->getCookieValue('recently_viewed');

        $this->assertCount(12, $cookie);
    }

    public function test_product_page_shows_recently_viewed_section(): void
    {
        $productA = $this->createProduct(['slug' => 'first', 'name_ru' => 'First Product']);
        $productB = $this->createProduct(['slug' => 'second', 'name_ru' => 'Second Product']);

        $this->get(route('product.show', $productA->slug));

        $response = $this->get(route('product.show', $productB->slug));

        $response->assertOk();
        $response->assertSee('Недавно просмотренные');
        $response->assertSee('First Product');
    }

    public function test_product_page_does_not_show_current_product_in_recently_viewed(): void
    {
        $product = $this->createProduct(['slug' => 'only', 'name_ru' => 'Only Product']);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertOk();
        $response->assertDontSee('Недавно просмотренные');
    }

    private function getCookieValue(string $name): ?array
    {
        $cookies = $this->response->headers->getCookies();

        foreach ($cookies as $cookie) {
            if ($cookie->getName() === $name) {
                $value = $cookie->getValue();
                $decoded = json_decode((string) $value, true);

                return is_array($decoded) ? $decoded : null;
            }
        }

        return null;
    }

    private function createProduct(array $overrides = []): Product
    {
        $brand = Brand::create([
            'slug' => 'brand-'.Str::lower(Str::random(6)),
            'name' => 'Brand '.Str::random(4),
            'is_active' => true,
            'logo' => 'brands/logo.png',
        ]);

        $category = Category::create([
            'slug' => 'category-'.Str::lower(Str::random(6)),
            'name_ru' => 'Категория',
            'name_by' => 'Катэгорыя',
            'is_active' => true,
        ]);

        $product = Product::create(array_merge([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'slug' => 'product-'.Str::lower(Str::random(6)),
            'name_ru' => 'Test Product',
            'name_by' => 'Test Product',
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
        ], $overrides));

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::upper(Str::random(8)),
            'volume_ml' => '50',
            'price_usd' => 100,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/test.jpg',
            'sort_order' => 0,
            'alt_ru' => $product->name_ru,
            'alt_by' => $product->name_by,
        ]);

        return $product;
    }
}
