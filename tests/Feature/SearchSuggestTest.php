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

class SearchSuggestTest extends TestCase
{
    use RefreshDatabase;

    public function test_suggest_returns_empty_for_short_query(): void
    {
        $response = $this->getJson(route('search.suggest', ['q' => 'a']));

        $response->assertOk();
        $response->assertExactJson([]);
    }

    public function test_suggest_returns_empty_for_empty_query(): void
    {
        $response = $this->getJson(route('search.suggest', ['q' => '']));

        $response->assertOk();
        $response->assertExactJson([]);
    }

    public function test_suggest_finds_product_by_exact_name(): void
    {
        $product = $this->createProductWithVariant(['name_ru' => 'Dior Sauvage'], ['price_usd' => 100, 'sku' => 'SKU001']);

        $response = $this->getJson(route('search.suggest', ['q' => 'Dior Sauvage']));

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'Dior Sauvage']);
    }

    public function test_suggest_finds_product_by_partial_name(): void
    {
        $this->createProductWithVariant(['name_ru' => 'Chanel No5'], ['price_usd' => 80]);

        $response = $this->getJson(route('search.suggest', ['q' => 'chanel']));

        $response->assertOk();
        $response->assertJsonCount(1);
    }

    public function test_suggest_finds_product_by_sku(): void
    {
        $this->createProductWithVariant(['name_ru' => 'Test'], ['price_usd' => 50, 'sku' => 'XYZ12345']);

        $response = $this->getJson(route('search.suggest', ['q' => 'XYZ12345']));

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['sku' => 'XYZ12345']);
    }

    public function test_suggest_finds_product_by_brand(): void
    {
        $brand = Brand::create([
            'slug' => 'dior',
            'name' => 'Dior',
            'is_active' => true,
            'logo' => 'brands/dior.png',
        ]);

        $category = Category::create([
            'slug' => 'parfum',
            'name_ru' => 'Парфюмерия',
            'name_by' => 'Парфумерыя',
            'is_active' => true,
        ]);

        $product = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'slug' => 'dior-sauvage',
            'name_ru' => 'Sauvage',
            'name_by' => 'Sauvage',
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
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::upper(Str::random(6)),
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

        $response = $this->getJson(route('search.suggest', ['q' => 'Dior']));

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, count($response->json()));
    }

    public function test_suggest_returns_max_8_results(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->createProductWithVariant(
                ['name_ru' => 'Arome '.$i, 'slug' => 'arome-'.$i],
                ['price_usd' => 50, 'sku' => 'SKU00'.$i]
            );
        }

        $response = $this->getJson(route('search.suggest', ['q' => 'Arome']));

        $response->assertOk();
        $this->assertLessThanOrEqual(8, count($response->json()));
    }

    public function test_suggest_returns_json_with_expected_keys(): void
    {
        $this->createProductWithVariant(['name_ru' => 'Boss'], ['price_usd' => 60]);

        $response = $this->getJson(route('search.suggest', ['q' => 'boss']));

        $response->assertOk();
        $item = $response->json()[0];

        $this->assertArrayHasKey('id', $item);
        $this->assertArrayHasKey('name', $item);
        $this->assertArrayHasKey('slug', $item);
        $this->assertArrayHasKey('brand', $item);
        $this->assertArrayHasKey('price_byn', $item);
        $this->assertArrayHasKey('image', $item);
    }

    private function createProductWithVariant(array $productOverrides = [], array $variantOverrides = []): Product
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
            'name_ru' => 'Товар',
            'name_by' => 'Тавар',
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

        ProductVariant::create(array_merge([
            'product_id' => $product->id,
            'sku' => 'SKU-'.Str::upper(Str::random(8)),
            'volume_ml' => '50',
            'price_usd' => 100,
            'is_active' => true,
        ], $variantOverrides));

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
