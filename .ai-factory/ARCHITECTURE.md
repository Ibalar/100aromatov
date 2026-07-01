# Архитектура: Structured Modules (Technical Layers)

## Обзор
Structured Modules — domain-aware модульная архитектура. Каждый модуль инкапсулирует функциональную область со своими контроллерами, сервисами и репозиториями. В отличие от Layered Architecture, здесь enforced богатые доменные модели и внедрение зависимостей через интерфейсы. Проект уже следует этому паттерну — формализуем правила.

## Обоснование выбора
- **Тип проекта:** интернет-магазин парфюмерии (средняя доменная сложность)
- **Техстек:** Laravel 12, Eloquent, MoonShine 4
- **Ключевой фактор:** проект уже структурирован по модульному принципу (Controllers, Services, Models, MoonShine Resources) — формализация правил закрепит существующую практику

## Структура папок
```
app/
├── Http/
│   ├── Controllers/          # HTTP-обработчики (19 контроллеров)
│   └── Middleware/           # SetLocale, ForceHttpsForMoonShine
├── Models/                   # Богатые доменные модели Eloquent (23 модели)
│                             # Содержат бизнес-правила, мутаторы, скоупы
├── Services/                 # Application Services — оркестрация use cases (7 сервисов)
│                             # НЕ содержат доменной логики — только вызовы моделей + инфраструктура
├── MoonShine/
│   ├── Resources/            # CRUD-ресурсы админ-панели (16 групп)
│   ├── Pages/                # Кастомные страницы админки
│   └── Layouts/              # Макет MoonShine
├── Observers/                # Наблюдатели Eloquent (ProductVariantObserver)
├── Providers/                # Сервис-провайдеры, DI-контейнер
└── Support/                  # Хелперы (helpers.php)
config/                       # 15 конфигурационных файлов
database/
├── migrations/               # 60 миграций
├── seeders/                  # Сидеры
└── factories/                # Фабрики
resources/views/              # ~55 Blade-шаблонов
├── components/               # Переиспользуемые Blade-компоненты
├── layouts/                  # Базовый макет
├── partials/                 # Частичные представления (header, footer, modals)
├── products/                 # Представления каталога
├── checkout/                 # Представления оформления заказа
├── cart/                     # Представления корзины
├── customer/                 # Личный кабинет (auth, account)
└── vendor/pagination/        # Кастомная пагинация
routes/                       # web.php, console.php
tests/                        # PHPUnit-тесты
```

## Правила зависимостей

Направление зависимостей строго вниз — внутренние слои не зависят от внешних:

```
Controllers → Services → Models
     ↓
  Middleware (сквозные задачи: локаль, HTTPS)
```

- ✅ Контроллеры вызывают сервисы, сервисы работают с моделями
- ✅ Сервисы получают зависимости через конструктор (DI)
- ✅ MoonShine Resources могут вызывать и сервисы, и модели напрямую (админ-панель)
- ❌ Контроллеры не вызывают модели напрямую (без сервиса-посредника)
- ❌ Модели не вызывают сервисы и контроллеры
- ❌ Сервисы не вызывают контроллеры

## Коммуникация между слоями

- **HTTP-запрос → Контроллер:** валидация через FormRequest, вызов сервиса, возврат view/redirect
- **Контроллер → Сервис:** вызов метода сервиса с DTO/параметрами
- **Сервис → Модель:** вызов методов модели (бизнес-правила внутри модели)
- **Сервис → Инфраструктура:** email, Telegram, логи — через фасады или внедрённые зависимости
- **Модель → БД:** Eloquent ORM (активная запись)
- **Observer → Логирование:** ProductVariantObserver → PriceLog

## Ключевые принципы

1. **Богатые доменные модели:** бизнес-правила, инварианты и мутации состояния живут в моделях, не в сервисах. Сервисы — оркестраторы, модели — носители логики.

2. **Тонкие контроллеры:** контроллер принимает запрос, валидирует, дёргает 1-2 метода сервиса, возвращает ответ. Никакой бизнес-логики в контроллерах.

3. **Внедрение зависимостей:** все внешние зависимости (сервисы, репозитории, gateway) внедряются через конструктор. Фасады допустимы для инфраструктурных вызовов (Log, Mail).

4. **Общие ресурсы минимальны:** `app/Support/helpers.php` — только утилиты без бизнес-логики. `resources/views/components/` — только переиспользуемые UI-компоненты.

5. **Подготовка к Explicit Architecture:** репозитории используют интерфейс (`interface` + Eloquent-реализация), что упростит миграцию при необходимости.

## Примеры кода

### Богатая доменная модель
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function markAsPaid(): void
    {
        if ($this->status !== 'new') {
            throw new \RuntimeException('Только новый заказ можно оплатить');
        }

        $this->status = 'paid';
        $this->paid_at = now();
        $this->save();
    }

    public function addItem(ProductVariant $variant, int $quantity): OrderItem
    {
        if ($this->status !== 'new') {
            throw new \RuntimeException('Нельзя изменить оформленный заказ');
        }

        if ($variant->stock < $quantity) {
            throw new \RuntimeException('Недостаточно товара на складе');
        }

        return $this->items()->create([
            'product_variant_id' => $variant->id,
            'quantity' => $quantity,
            'price' => $variant->price,
        ]);
    }

    public function recalculateTotal(): void
    {
        $this->total = $this->items->sum(fn ($item) => $item->price * $item->quantity);
        $this->save();
    }
}
```

### Application Service (оркестратор)
```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\PromoCode;

class OrderService
{
    public function __construct(
        private TelegramService $telegram,
    ) {}

    public function placeOrder(array $data, ?PromoCode $promoCode): Order
    {
        $order = Order::create([
            'customer_id' => auth()->id(),
            'status' => 'new',
        ]);

        foreach ($data['items'] as $item) {
            $order->addItem(
                ProductVariant::findOrFail($item['variant_id']),
                $item['quantity']
            );
        }

        if ($promoCode) {
            $order->applyPromoCode($promoCode);
        }

        $order->recalculateTotal();
        $order->finalize();

        $this->telegram->notifyNewOrder($order);

        return $order;
    }
}
```

### Тонкий контроллер
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\OrderService;

class CheckoutController extends Controller
{
    public function store(CheckoutRequest $request, OrderService $orderService)
    {
        $order = $orderService->placeOrder(
            $request->validated(),
            PromoCode::findByCode($request->promo_code)
        );

        return redirect()->route('checkout.success', $order);
    }
}
```

## Анти-паттерны
- ❌ **Анемичные модели:** модели только с геттерами/сеттерами, вся логика в сервисах. Бизнес-правила должны быть в моделях.
- ❌ **Пропуск слоя:** контроллер напрямую вызывает `Order::create(...)` или `Product::where(...)` — без сервиса.
- ❌ **Восходящие зависимости:** модель вызывает `CartService` или `TelegramService`.
- ❌ **Божественные сервисы:** один сервис на 500+ строк, обрабатывающий несвязанные фичи.
