# План: Email-уведомления

> **Ветка:** feature/email-notifications
> **Дата:** 2026-06-30
> **Описание:** Отправка email-уведомлений клиенту о заказе и администратору о новом заказе через Laravel Mailable.

## Roadmap Linkage

- Milestone: "1.4 Email-уведомления"
- Rationale: Критичный компонент для запуска — клиент должен получить подтверждение заказа, администратор — уведомление о новом заказе.

## Settings

- **Testing:** yes
- **Logging:** verbose (DEBUG — отправка, WARN — ошибки, INFO — успех)
- **Docs:** yes (mandatory checkpoint)

## Research Context

> **Active Summary:** Mailable-классов нет. Notification-классов нет. `Customer` имеет `Notifiable` trait + email. `Order` имеет email, items, total. `Setting` имеет site email (не admin_email). MAIL_MAILER=log в .env — настроить SMTP. Почта клиента: `$order->email`. Почта админа: `Setting::getSettings()->email`.

---

## Задачи

### Фаза 1: Mailable-классы

- [x] **T1** — Создать `app/Mail/OrderConfirmation.php`
  - Mailable для клиента: тема «Заказ №X оформлен», HTML-шаблон с деталями заказа
  - Параметры конструктора: `Order $order`
  - Данные: номер заказа, статус, телефон, сумма, список товаров
  - from: `config('mail.from.address')` / `config('mail.from.name')`
  - **Файл:** `app/Mail/OrderConfirmation.php` (создать)
  - **Шаблон:** `resources/views/emails/order-confirmation.blade.php` (создать)
  - **Логи:** `Log::debug` при построении, `Log::info` при отправке

- [x] **T2** — Создать `app/Mail/NewOrderNotification.php`
  - Mailable для администратора: тема «Новый заказ #X», детали заказа
  - Адрес: `Setting::getSettings()->email`
  - **Файл:** `app/Mail/NewOrderNotification.php` (создать)
  - **Шаблон:** `resources/views/emails/new-order-notification.blade.php` (создать)
  - **Логи:** `Log::debug` при построении, `Log::info` при отправке

### Фаза 2: Интеграция в OrderService

- [x] **T3** — Добавить отправку email в `OrderService::create()`
  - После успешного создания заказа и Telegram-уведомления
  - Отправить `OrderConfirmation` на `$order->email` (если заполнен)
  - Отправить `NewOrderNotification` на `Setting::getSettings()->email` (если заполнен)
  - Обернуть каждую отправку в try/catch — email не должен блокировать создание заказа
  - **Файл:** `app/Services/OrderService.php` (изменить)
  - **Логи:** `Log::info` при успехе, `Log::warning` при ошибке (с `order_id`)

### Фаза 3: Тесты

- [x] **T4** — Тесты OrderConfirmation Mailable
  - Проверить тему, получателя, содержимое (номер заказа, сумма)
  - **Файл:** `tests/Feature/OrderConfirmationMailTest.php` (создать)

- [x] **T5** — Тесты интеграции в OrderService
  - Проверить что email отправляется при оформлении заказа
  - Проверить что заказ создаётся даже при ошибке отправки email
  - **Файл:** `tests/Feature/OrderEmailNotificationTest.php` (создать)

### Фаза 4: Документация

- [x] **T6** — Обновить `.env.example`
  - Добавить комментарий к MAIL_MAILER: `# smtp для реальной отправки, log для разработки`
  - Добавить MAIL_FROM_ADDRESS с реальным значением
  - **Файл:** `.env.example` (изменить)

## Commit Plan

| # | Коммит | Задачи |
|---|--------|--------|
| 1 | `feat(mail): add OrderConfirmation and NewOrderNotification mailables` | T1, T2 |
| 2 | `feat(order): send email notifications on order creation` | T3 |
| 3 | `test(mail): add email notification tests` | T4, T5 |
| 4 | `docs: update .env.example with mail settings` | T6 |
