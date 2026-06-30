<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class OrderStatusTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_be_created_with_new_status(): void
    {
        $order = $this->createOrder();

        $this->assertTrue($order->isNew());
        $this->assertInstanceOf(OrderStatus::class, $order->status);
        $this->assertSame(OrderStatus::New, $order->status);
    }

    public function test_transition_new_to_paid(): void
    {
        $order = $this->createOrder();

        $order->transitionTo(OrderStatus::Paid);

        $this->assertTrue($order->isPaid());
        $this->assertSame(OrderStatus::Paid, $order->fresh()->status);
    }

    public function test_transition_new_to_canceled(): void
    {
        $order = $this->createOrder();

        $order->transitionTo(OrderStatus::Canceled);

        $this->assertTrue($order->isCanceled());
    }

    public function test_transition_paid_to_processing(): void
    {
        $order = $this->createOrder();
        $order->transitionTo(OrderStatus::Paid);

        $order->transitionTo(OrderStatus::Processing);

        $this->assertTrue($order->isProcessing());
    }

    public function test_transition_processing_to_shipped(): void
    {
        $order = $this->createOrder();
        $order->transitionTo(OrderStatus::Paid);
        $order->transitionTo(OrderStatus::Processing);

        $order->transitionTo(OrderStatus::Shipped);

        $this->assertTrue($order->isShipped());
    }

    public function test_transition_shipped_to_completed(): void
    {
        $order = $this->createOrder();
        $order->transitionTo(OrderStatus::Paid);
        $order->transitionTo(OrderStatus::Processing);
        $order->transitionTo(OrderStatus::Shipped);

        $order->transitionTo(OrderStatus::Completed);

        $this->assertTrue($order->isCompleted());
    }

    public function test_invalid_transition_throws_exception(): void
    {
        $order = $this->createOrder();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Недопустимый переход статуса');

        $order->transitionTo(OrderStatus::Shipped);
    }

    public function test_cannot_transition_from_terminal_completed(): void
    {
        $order = $this->createOrder();
        $order->transitionTo(OrderStatus::Paid);
        $order->transitionTo(OrderStatus::Processing);
        $order->transitionTo(OrderStatus::Shipped);
        $order->transitionTo(OrderStatus::Completed);

        $this->expectException(RuntimeException::class);

        $order->transitionTo(OrderStatus::New);
    }

    public function test_cannot_transition_from_terminal_canceled(): void
    {
        $order = $this->createOrder();
        $order->transitionTo(OrderStatus::Canceled);

        $this->expectException(RuntimeException::class);

        $order->transitionTo(OrderStatus::New);
    }

    public function test_cannot_transition_new_to_processing_directly(): void
    {
        $order = $this->createOrder();

        $this->expectException(RuntimeException::class);

        $order->transitionTo(OrderStatus::Processing);
    }

    public function test_cannot_transition_new_to_shipped_directly(): void
    {
        $order = $this->createOrder();

        $this->expectException(RuntimeException::class);

        $order->transitionTo(OrderStatus::Shipped);
    }

    public function test_status_helpers_return_correct_booleans(): void
    {
        $order = $this->createOrder();

        $this->assertTrue($order->isNew());
        $this->assertFalse($order->isPaid());
        $this->assertFalse($order->isProcessing());
        $this->assertFalse($order->isShipped());
        $this->assertFalse($order->isCompleted());
        $this->assertFalse($order->isCanceled());

        $order->transitionTo(OrderStatus::Canceled);

        $this->assertFalse($order->isNew());
        $this->assertTrue($order->isCanceled());
    }

    public function test_is_confirmed_backward_compatibility(): void
    {
        $order = $this->createOrder();
        $order->transitionTo(OrderStatus::Paid);
        $order->transitionTo(OrderStatus::Processing);
        $order->transitionTo(OrderStatus::Shipped);
        $order->transitionTo(OrderStatus::Completed);

        $this->assertTrue($order->isConfirmed());
    }

    public function test_is_confirmed_returns_false_when_not_completed(): void
    {
        $order = $this->createOrder();

        $this->assertFalse($order->isConfirmed());
    }

    public function test_can_cancel_from_paid(): void
    {
        $order = $this->createOrder();
        $order->transitionTo(OrderStatus::Paid);

        $order->transitionTo(OrderStatus::Canceled);

        $this->assertTrue($order->isCanceled());
    }

    public function test_can_cancel_from_processing(): void
    {
        $order = $this->createOrder();
        $order->transitionTo(OrderStatus::Paid);
        $order->transitionTo(OrderStatus::Processing);

        $order->transitionTo(OrderStatus::Canceled);

        $this->assertTrue($order->isCanceled());
    }

    private function createOrder(): Order
    {
        $brand = Brand::create([
            'slug' => 'brand-' . Str::lower(Str::random(6)),
            'name' => 'Test Brand',
            'is_active' => true,
            'logo' => 'brands/logo.png',
        ]);

        $category = Category::create([
            'slug' => 'category-' . Str::lower(Str::random(6)),
            'name_ru' => 'Тестовая категория',
            'name_by' => 'Тэставая катэгорыя',
            'is_active' => true,
        ]);

        $product = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'slug' => 'product-' . Str::lower(Str::random(6)),
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

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-' . Str::upper(Str::random(8)),
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

        return Order::create([
            'status' => 'new',
            'total_usd' => 100,
            'total_byn' => 320,
            'phone' => '+375291112233',
            'call_preference' => 'call_me',
            'email' => 'buyer@example.com',
        ]);
    }
}
