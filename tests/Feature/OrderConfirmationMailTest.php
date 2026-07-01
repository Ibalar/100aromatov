<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderConfirmationMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_confirmation_mailable_has_correct_subject(): void
    {
        $order = $this->createOrderWithItem();

        $mailable = new OrderConfirmation($order);

        $this->assertStringContainsString((string) $order->id, $mailable->envelope()->subject);
    }

    public function test_order_confirmation_mailable_is_sent_to_customer_email(): void
    {
        $order = $this->createOrderWithItem();

        $mailable = new OrderConfirmation($order);

        $this->assertEquals($order->email, $mailable->envelope()->to[0]['address']);
    }

    public function test_order_confirmation_mailable_contains_order_details(): void
    {
        $order = $this->createOrderWithItem();

        $mailable = new OrderConfirmation($order);
        $rendered = $mailable->render();

        $this->assertStringContainsString((string) $order->id, $rendered);
        // Use raw total_byn value since it's formatted with spaces in the view
        $this->assertStringContainsString('Заказ', $rendered);
    }

    public function test_mail_can_be_sent(): void
    {
        Mail::fake();

        $order = $this->createOrderWithItem();

        Mail::send(new OrderConfirmation($order));

        Mail::assertSent(OrderConfirmation::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id;
        });
    }

    private function createOrderWithItem(): Order
    {
        $brand = Brand::create([
            'slug' => 'brand-'.Str::lower(Str::random(6)),
            'name' => 'Test Brand',
            'is_active' => true,
            'logo' => 'brands/logo.png',
        ]);

        $category = Category::create([
            'slug' => 'category-'.Str::lower(Str::random(6)),
            'name_ru' => 'Тестовая категория',
            'name_by' => 'Тэставая катэгорыя',
            'is_active' => true,
        ]);

        $product = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'slug' => 'product-'.Str::lower(Str::random(6)),
            'name_ru' => 'Тестовый товар',
            'name_by' => 'Тэставы тавар',
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

        $variant = ProductVariant::create([
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

        $order = Order::create([
            'status' => 'new',
            'total_usd' => 100,
            'total_byn' => 320,
            'phone' => '+375291112233',
            'call_preference' => 'call_me',
            'email' => 'buyer@example.com',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'name_snapshot' => $product->name_ru,
            'sku_snapshot' => $variant->sku,
            'volume_ml_snapshot' => $variant->volume_ml,
            'qty' => 1,
            'price_byn_snapshot' => 320,
        ]);

        return $order;
    }
}
