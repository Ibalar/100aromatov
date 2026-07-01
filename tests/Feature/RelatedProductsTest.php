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

class RelatedProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_have_related_products(): void
    {
        $product = $this->createProduct(['slug' => 'main']);
        $related = $this->createProduct(['slug' => 'related']);

        $product->relatedProducts()->attach($related->id);

        $this->assertCount(1, $product->fresh()->relatedProducts);
        $this->assertEquals($related->id, $product->fresh()->relatedProducts->first()->id);
    }

    public function test_product_page_shows_related_products(): void
    {
        $product = $this->createProduct(['slug' => 'main', 'name_ru' => 'Основной товар']);
        $related = $this->createProduct(['slug' => 'related', 'name_ru' => 'Похожий товар']);

        $product->relatedProducts()->attach($related->id);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertOk();
        $response->assertSee('С этим покупают');
        $response->assertSee('Похожий товар');
    }

    public function test_product_page_without_related_does_not_show_section(): void
    {
        $product = $this->createProduct(['slug' => 'lonely']);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertOk();
        $response->assertDontSee('С этим покупают');
    }

    public function test_related_products_are_symmetrical_via_second_side(): void
    {
        $productA = $this->createProduct(['slug' => 'a']);
        $productB = $this->createProduct(['slug' => 'b']);

        $productA->relatedProducts()->attach($productB->id);

        // Reverse: B has A as related
        $productB->relatedProducts()->attach($productA->id);

        $this->assertCount(1, $productA->fresh()->relatedProducts);
        $this->assertCount(1, $productB->fresh()->relatedProducts);
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
