# План: Enum-статусы заказа

> **Ветка:** feature/order-status-enum
> **Дата:** 2026-06-30
> **Описание:** Внедрить PHP Enum `OrderStatus` с методами переходов, заменить строковые статусы в модели, админке и фронтенде.

## Roadmap Linkage

- Milestone: "1.3 Enum-статусы заказа"
- Rationale: Статусы new/paid/processing/shipped/completed/canceled — базовая механика обработки заказов. Сейчас только 'new', нет переходов, нет валидации.

## Settings

- **Testing:** yes
- **Logging:** verbose (DEBUG для смены статуса, WARN для невалидных переходов)
- **Docs:** yes (mandatory checkpoint)

## Research Context

> **Active Summary:** В проекте нет `app/Enums/`. Статус хранится как строка в БД (`VARCHAR`, default `'new'`). В модели есть `isNew()`, `isConfirmed()` — но `confirmed` нигде не выставляется. В MoonShine — текстовое поле без ограничений. На фронтенде — сырая строка без перевода. Единственное место установки статуса: `OrderService::create()` → `'new'`.

---

## Задачи

### Фаза 1: Enum и модель

- [x] **T1** — Создать `app/Enums/OrderStatus.php`
  - PHP 8.1 backed enum: `string` type
  - Кейсы: `New = 'new'`, `Paid = 'paid'`, `Processing = 'processing'`, `Shipped = 'shipped'`, `Completed = 'completed'`, `Canceled = 'canceled'`
  - Метод `label(): string` — русские названия для каждого статуса
  - Метод `allowedTransitions(): array` — список допустимых следующих статусов:
    - `New → [Paid, Canceled]`
    - `Paid → [Processing, Canceled]`
    - `Processing → [Shipped, Canceled]`
    - `Shipped → [Completed]`
    - `Completed → []` (терминальный)
    - `Canceled → []` (терминальный)
  - Метод `canTransitionTo(OrderStatus $target): bool`
  - Метод `isTerminal(): bool`
  - **Файл:** `app/Enums/OrderStatus.php` (создать)

- [x] **T2** — Обновить `app/Models/Order.php`
  - Добавить `$casts['status'] => OrderStatus::class` (Laravel 9+ enum casting)
  - Добавить метод `transitionTo(OrderStatus $newStatus): void`:
    - Проверка `status->canTransitionTo($newStatus)` → иначе `RuntimeException`
    - `$this->status = $newStatus; $this->save();`
    - `Log::info('Order: status changed', ['order_id' => $this->id, 'from' => $oldStatus, 'to' => $newStatus])`
  - Добавить методы-хелперы: `isNew()`, `isPaid()`, `isProcessing()`, `isShipped()`, `isCompleted()`, `isCanceled()`
  - Сохранить существующие `isNew()`, `isConfirmed()` для обратной совместимости (deprecated warning в комментарии), `isConfirmed()` → алиас на `isCompleted()`
  - **Файл:** `app/Models/Order.php` (изменить)
  - **Логи:** `Log::debug` при попытке перехода, `Log::info` при успешном переходе

### Фаза 2: MoonShine админка

- [x] **T3** — Заменить текстовое поле статуса на `Select` в MoonShine
  - `OrderFormPage.php`: `Select::make('Статус', 'status')->options(OrderStatus::labels())`
  - `OrderIndexPage.php`: заменить `Text::make` на отображение с badge-цветом через `ChangeLog` или кастомный вывод
  - `OrderDetailPage.php`: badge-отображение статуса
  - **Файлы:** `app/MoonShine/Resources/Order/Pages/OrderFormPage.php`, `OrderIndexPage.php`, `OrderDetailPage.php` (изменить)

- [x] **T4** — Добавить actions для смены статуса в MoonShine
  - Action-кнопки на OrderDetailPage: «Оплачен», «В обработку», «Отправлен», «Завершён», «Отменён»
  - Каждая кнопка видна только если переход разрешён (`canTransitionTo`)
  - Action вызывает `$order->transitionTo(OrderStatus::...)` через контроллер
  - **Файлы:** `app/MoonShine/Resources/Order/OrderResource.php`, `app/MoonShine/Resources/Order/Pages/OrderDetailPage.php` (изменить)
  - **Логи:** `Log::info('Order: admin changed status', ['order_id' => ..., 'from' => ..., 'to' => ..., 'user_id' => ...])`

### Фаза 3: Фронтенд

- [x] **T5** — Локализовать отображение статуса в личном кабинете
  - `resources/views/customer/account/orders.blade.php`: выводить `$order->status->label()` вместо сырой строки
  - Добавить badge-классы в зависимости от статуса (new=синий, paid=зелёный, processing=жёлтый и т.д.)
  - **Файл:** `resources/views/customer/account/orders.blade.php` (изменить)

### Фаза 4: Тесты

- [x] **T6** — Тесты OrderStatus enum
  - Все 6 кейсов создаются корректно
  - `label()` возвращает русские названия
  - `allowedTransitions()` возвращает правильные списки
  - `canTransitionTo()` для валидных и невалидных переходов
  - `isTerminal()` для Completed и Canceled
  - **Файл:** `tests/Unit/OrderStatusTest.php` (создать)

- [ ] **T7** — Тесты модели Order (переходы)
  - `transitionTo()` успешно меняет статус
  - `transitionTo()` бросает исключение при невалидном переходе
  - Хелперы `isPaid()`, `isProcessing()` и т.д. работают корректно
  - Enum casting работает через Eloquent
  - **Файл:** `tests/Feature/OrderStatusTransitionTest.php` (создать)

### Фаза 5: Документация

- [x] **T8** — Документация (docs/ на другой ветке, обновлена в AGENTS.md)
  - `docs/architecture.md`: добавить `app/Enums/` в структуру, описать flow статусов
  - **Файл:** `docs/architecture.md` (изменить)

## Commit Plan

| # | Коммит | Задачи |
|---|--------|--------|
| 1 | `feat(order): add OrderStatus enum with transition rules` | T1, T2 |
| 2 | `feat(order): replace status text field with Select in MoonShine` | T3, T4 |
| 3 | `feat(order): localize order status on customer frontend` | T5 |
| 4 | `test(order): add enum and transition tests` | T6, T7 |
| 5 | `docs(order): update architecture with enum pattern` | T8 |
