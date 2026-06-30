<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case Paid = 'paid';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Canceled = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Новый',
            self::Paid => 'Оплачен',
            self::Processing => 'В обработке',
            self::Shipped => 'Отправлен',
            self::Completed => 'Завершён',
            self::Canceled => 'Отменён',
        };
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::New => [self::Paid, self::Canceled],
            self::Paid => [self::Processing, self::Canceled],
            self::Processing => [self::Shipped, self::Canceled],
            self::Shipped => [self::Completed],
            self::Completed => [],
            self::Canceled => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    public function isTerminal(): bool
    {
        return $this === self::Completed || $this === self::Canceled;
    }

    public static function labels(): array
    {
        return array_reduce(self::cases(), function (array $carry, self $case): array {
            $carry[$case->value] = $case->label();
            return $carry;
        }, []);
    }

    public static function labelFor(mixed $value): string
    {
        if ($value instanceof self) {
            return $value->label();
        }
        return self::tryFrom((string) $value)?->label() ?? (string) $value;
    }
}
