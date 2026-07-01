<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_all_six_cases_exist(): void
    {
        $cases = OrderStatus::cases();

        $this->assertCount(6, $cases);
    }

    public function test_labels_return_russian_names(): void
    {
        $this->assertSame('Новый', OrderStatus::New->label());
        $this->assertSame('Оплачен', OrderStatus::Paid->label());
        $this->assertSame('В обработке', OrderStatus::Processing->label());
        $this->assertSame('Отправлен', OrderStatus::Shipped->label());
        $this->assertSame('Завершён', OrderStatus::Completed->label());
        $this->assertSame('Отменён', OrderStatus::Canceled->label());
    }

    public function test_labels_method_returns_all(): void
    {
        $labels = OrderStatus::labels();

        $this->assertCount(6, $labels);
        $this->assertArrayHasKey('new', $labels);
        $this->assertArrayHasKey('paid', $labels);
        $this->assertSame('Новый', $labels['new']);
    }

    public function test_new_allowed_transitions(): void
    {
        $transitions = OrderStatus::New->allowedTransitions();

        $this->assertEqualsCanonicalizing(
            [OrderStatus::Paid, OrderStatus::Canceled],
            $transitions
        );
    }

    public function test_paid_allowed_transitions(): void
    {
        $transitions = OrderStatus::Paid->allowedTransitions();

        $this->assertEqualsCanonicalizing(
            [OrderStatus::Processing, OrderStatus::Canceled],
            $transitions
        );
    }

    public function test_processing_allowed_transitions(): void
    {
        $transitions = OrderStatus::Processing->allowedTransitions();

        $this->assertEqualsCanonicalizing(
            [OrderStatus::Shipped, OrderStatus::Canceled],
            $transitions
        );
    }

    public function test_shipped_allowed_transitions(): void
    {
        $transitions = OrderStatus::Shipped->allowedTransitions();

        $this->assertEqualsCanonicalizing(
            [OrderStatus::Completed],
            $transitions
        );
    }

    public function test_completed_has_no_transitions(): void
    {
        $transitions = OrderStatus::Completed->allowedTransitions();

        $this->assertEmpty($transitions);
    }

    public function test_canceled_has_no_transitions(): void
    {
        $transitions = OrderStatus::Canceled->allowedTransitions();

        $this->assertEmpty($transitions);
    }

    public function test_can_transition_to_valid_target(): void
    {
        $this->assertTrue(OrderStatus::New->canTransitionTo(OrderStatus::Paid));
        $this->assertTrue(OrderStatus::New->canTransitionTo(OrderStatus::Canceled));
        $this->assertTrue(OrderStatus::Paid->canTransitionTo(OrderStatus::Processing));
        $this->assertTrue(OrderStatus::Processing->canTransitionTo(OrderStatus::Shipped));
        $this->assertTrue(OrderStatus::Shipped->canTransitionTo(OrderStatus::Completed));
    }

    public function test_cannot_transition_to_invalid_target(): void
    {
        $this->assertFalse(OrderStatus::New->canTransitionTo(OrderStatus::Completed));
        $this->assertFalse(OrderStatus::New->canTransitionTo(OrderStatus::Processing));
        $this->assertFalse(OrderStatus::New->canTransitionTo(OrderStatus::Shipped));
        $this->assertFalse(OrderStatus::Paid->canTransitionTo(OrderStatus::New));
        $this->assertFalse(OrderStatus::Completed->canTransitionTo(OrderStatus::New));
        $this->assertFalse(OrderStatus::Canceled->canTransitionTo(OrderStatus::New));
    }

    public function test_terminal_statuses(): void
    {
        $this->assertTrue(OrderStatus::Completed->isTerminal());
        $this->assertTrue(OrderStatus::Canceled->isTerminal());
    }

    public function test_non_terminal_statuses(): void
    {
        $this->assertFalse(OrderStatus::New->isTerminal());
        $this->assertFalse(OrderStatus::Paid->isTerminal());
        $this->assertFalse(OrderStatus::Processing->isTerminal());
        $this->assertFalse(OrderStatus::Shipped->isTerminal());
    }

    public function test_enum_values_are_strings(): void
    {
        $this->assertSame('new', OrderStatus::New->value);
        $this->assertSame('paid', OrderStatus::Paid->value);
        $this->assertSame('processing', OrderStatus::Processing->value);
        $this->assertSame('shipped', OrderStatus::Shipped->value);
        $this->assertSame('completed', OrderStatus::Completed->value);
        $this->assertSame('canceled', OrderStatus::Canceled->value);
    }
}
