<?php

namespace Tests\Feature;

use App\Mail\NewOrderNotification;
use App\Mail\OrderConfirmation;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::create([
            'email' => 'admin@example.com',
            'usd_rate' => 3.2,
            'telegram_bot_token' => '',
            'telegram_chat_id' => '',
            'phones' => '{}',
            'metrics_head' => '',
            'metrics_body' => '',
        ]);
    }

    public function test_order_confirmation_email_is_sent_on_order_creation(): void
    {
        Mail::fake();

        $variant = $this->createVariant();

        $service = app(OrderService::class);
        $service->create([
            'phone' => '+375291112233',
            'call_preference' => 'call_me',
            'email' => 'buyer@example.com',
            'items' => [
                ['variant_id' => $variant->id, 'qty' => 1],
            ],
        ]);

        Mail::assertSent(OrderConfirmation::class, function ($mail) {
            return $mail->envelope()->to[0]['address'] === 'buyer@example.com';
        });
    }

    public function test_admin_notification_email_is_sent_on_order_creation(): void
    {
        Mail::fake();

        $variant = $this->createVariant();

        $service = app(OrderService::class);
        $service->create([
            'phone' => '+375291112233',
            'call_preference' => 'call_me',
            'email' => 'buyer@example.com',
            'items' => [
                ['variant_id' => $variant->id, 'qty' => 1],
            ],
        ]);

        Mail::assertSent(NewOrderNotification::class, function ($mail) {
            return $mail->envelope()->to[0]['address'] === 'admin@example.com';
        });
    }

    public function test_order_is_created_even_if_email_fails(): void
    {
        Mail::fake();
        Mail::shouldReceive('send')->andThrow(new \RuntimeException('SMTP error'));

        $variant = $this->createVariant();

        $service = app(OrderService::class);
        $order = $service->create([
            'phone' => '+375291112233',
            'call_preference' => 'call_me',
            'email' => 'buyer@example.com',
            'items' => [
                ['variant_id' => $variant->id, 'qty' => 1],
            ],
        ]);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_order_without_customer_email_skips_confirmation_mail(): void
    {
        Mail::fake();

        $variant = $this->createVariant();

        $service = app(OrderService::class);
        $service->create([
            'phone' => '+375291112233',
            'call_preference' => 'call_me',
            'items' => [
                ['variant_id' => $variant->id, 'qty' => 1],
            ],
        ]);

        // Confirmation should not be sent (no email), but admin can still receive
        Mail::assertNotSent(OrderConfirmation::class);
    }

    private function createVariant(): ProductVariant
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
            'sale_price_usd' => null,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/test.jpg',
            'sort_order' => 0,
            'alt_ru' => $product->name_ru,
            'alt_by' => $product->name_by,
        ]);

        return $variant;
    }
}
