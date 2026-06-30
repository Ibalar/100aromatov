# План: Checkout (Frontend + Backend)

> **Ветка:** feature/checkout-frontend-backend
> **Дата:** 2026-06-30
> **Описание:** Завершение формы оформления заказа — валидация, обработка ошибок, страница успеха, логирование.

## Roadmap Linkage

- Milestone: "1.1 Checkout (Frontend + Backend)"
- Rationale: Checkout — критичный путь продаж. Форма, валидация и Telegram уже реализованы. Осталось: FormRequest, страница успеха, обработка ошибок с логированием.

## Settings

- **Testing:** yes
- **Logging:** verbose (DEBUG для ключевых точек)
- **Docs:** yes (mandatory checkpoint)

## Research Context

> **Active Summary (checkout gap analysis):**
> CheckoutController уже реализован: форма, валидация inline, вызов OrderService, Telegram-уведомление. Гэпы: нет FormRequest, нет страницы успеха, нет try/catch вокруг OrderService::create(), нет логирования ошибок оформления. DTO для cart/order — roadmap 5.3. Enum-статусы и Email — roadmap 1.3/1.4.

---

## Задачи

### Фаза 1: FormRequest

- [x] **T1** — Создать `app/Http/Requests/CheckoutRequest.php`
  - Класс FormRequest с правилами валидации из `CheckoutController::store()`
  - Поля: `phone`, `call_preference`, `email`, `promo_code`, `privacy_policy`, `website` (honeypot), `form_started_at`, `items`
  - Метод `rules()`: миграция inline-правил из контроллера
  - Метод `messages()`: кастомные сообщения на русском
  - **Файл:** `app/Http/Requests/CheckoutRequest.php` (создать)
  - **Логи:** `Log::debug('CheckoutRequest: validation passed', ['phone' => $this->maskPhone(...)])`

- [x] **T2** — Заменить inline-валидацию в `CheckoutController::store()` на внедрение `CheckoutRequest`
  - Удалить `$request->validate(...)` вызовы, заменить сигнатуру на `CheckoutRequest $request`
  - Удалить ручную проверку `isValidBelarusMobilePhone` — перенести в FormRequest
  - Удалить ручную проверку таймера — перенести в FormRequest
  - **Файл:** `app/Http/Controllers/CheckoutController.php` (изменить)
  - **Логи:** сохранить `Log::debug` в контроллере после успешного создания заказа

### Фаза 2: Страница успеха

- [x] **T3** — Создать маршрут и представление страницы успеха
  - Маршрут: `GET /checkout/success/{order}` → `checkout.success`
  - Представление: `resources/views/checkout/success.blade.php`
  - Отображение: номер заказа, статус, телефон для связи, сумма, список товаров
  - **Файлы:** `routes/web.php`, `resources/views/checkout/success.blade.php` (создать)
  - **Логи:** `Log::info('Checkout: success page viewed', ['order_id' => $order->id])`

- [x] **T4** — Изменить редирект после оформления на `checkout.success`
  - Заменить `redirect()->route('checkout.index')->with('success_order_id', ...)` на `redirect()->route('checkout.success', $order)`
  - Передать данные заказа через сессию или route model binding
  - **Файл:** `app/Http/Controllers/CheckoutController.php` (изменить)
  - **Логи:** `Log::info('Checkout: order created', ['order_id' => $order->id, 'total_byn' => $order->total_byn])`

### Фаза 3: Обработка ошибок и логирование

- [x] **T5** — Добавить try/catch и логирование в `CheckoutController::store()`
  - Обернуть `$service->create()` в `try/catch (\Throwable $e)`
  - При ошибке: `Log::error('Checkout: order creation failed', ['error' => $e->getMessage(), 'data' => ...])`
  - Вернуть пользователю понятное сообщение (не raw exception)
  - **Файл:** `app/Http/Controllers/CheckoutController.php` (изменить)

- [x] **T6** — Добавить логирование в `OrderService::create()`
  - `Log::debug` при старте создания заказа (phone, items_count)
  - `Log::info` после успешного создания (order_id, total)
  - `Log::warning` если Telegram не отправился (уже есть)
  - **Файл:** `app/Services/OrderService.php` (изменить)

### Фаза 4: Тесты

- [x] **T7** — Дополнить существующие тесты проверкой FormRequest
  - Проверить валидацию: пустой телефон, некорректный телефон, неотмеченный privacy_policy, пустая корзина
  - Проверить honeypot: заполненное поле `website` → ошибка
  - Проверить таймер: `form_started_at` слишком свежий → ошибка
  - **Файл:** `tests/Feature/CheckoutRequestTest.php` (создать)

- [x] **T8** — Тест страницы успеха
  - Создать заказ → проверить редирект на `checkout.success`
  - Проверить отображение номера заказа и суммы
  - **Файл:** `tests/Feature/CheckoutSuccessTest.php` (создать)

### Фаза 5: Документация

- [x] **T9** — Обновить документацию (`docs/architecture.md`)
  - Добавить CheckoutRequest в список компонентов
  - Обновить flow оформления заказа
  - **Файл:** `docs/architecture.md` (изменить)

## Commit Plan

| # | Коммит | Задачи |
|---|--------|--------|
| 1 | `feat(checkout): add CheckoutFormRequest with validation rules` | T1, T2 |
| 2 | `feat(checkout): add success page after order placement` | T3, T4 |
| 3 | `fix(checkout): add error handling and logging` | T5, T6 |
| 4 | `test(checkout): add FormRequest and success page tests` | T7, T8 |
| 5 | `docs(checkout): update architecture docs` | T9 |
