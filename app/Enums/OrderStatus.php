<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Facades\Log;

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
            self::New => __('Новый'),
            self::Paid => __('Оплачен'),
            self::Processing => __('В обработке'),
            self::Shipped => __('Отправлен'),
            self::Completed => __('Завершён'),
            self::Canceled => __('Отменён'),
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
        $allowed = $this->allowedTransitions();

        $can = in_array($target, $allowed, true);

        if (! $can) {
            Log::debug('OrderStatus: invalid transition', [
                'from' => $this->value,
                'to' => $target->value,
                'allowed' => array_map(fn ($s) => $s->value, $allowed),
            ]);
        }

        return $can;
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
}
