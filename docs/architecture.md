[← Начало работы](getting-started.md) · [Документация](../README.md) · [Деплой →](deployment.md)

# Архитектура

## Обзор

Проект следует паттерну **Structured Modules (Technical Layers)** — модульная архитектура с разделением на контроллеры, сервисы и модели. Бизнес-правила находятся в моделях, сервисы оркестрируют use cases.

Подробные архитектурные правила — в [`.ai-factory/ARCHITECTURE.md`](../.ai-factory/ARCHITECTURE.md).

## Структура проекта

```
app/
├── Http/
│   ├── Controllers/     # 19 контроллеров (каталог, корзина, заказы, SEO...)
│   ├── Requests/        # FormRequest-классы (CheckoutRequest)
│   └── Middleware/      # SetLocale, ForceHttpsForMoonShine
├── Models/              # 23 модели Eloquent
├── MoonShine/
│   ├── Resources/       # 16 CRUD-ресурсов админ-панели
│   ├── Pages/           # Страницы админ-панели
│   └── Layouts/         # Макет MoonShine
├── Services/            # 7 сервисов (Cart, Order, Telegram, Wishlist...)
├── Observers/           # ProductVariantObserver (PriceLog)
├── Providers/           # AppServiceProvider, MoonShineServiceProvider
└── Support/             # helpers.php
config/                  # 15 конфигурационных файлов
database/
├── migrations/          # 60 миграций
├── seeders/             # DatabaseSeeder, RolesAndPermissionsSeeder
└── factories/           # UserFactory
resources/views/         # ~55 Blade-шаблонов
routes/                  # web.php, console.php
tests/                   # PHPUnit-тесты
```

## Технический стек

| Компонент | Технология |
|-----------|-----------|
| Язык | PHP 8.2 |
| Фреймворк | Laravel 12 |
| БД (основная) | MySQL 8.4 |
| БД (тесты) | SQLite |
| ORM | Eloquent |
| Админ-панель | MoonShine 4.x + TinyMCE |
| Фронтенд | Blade + Tailwind CSS 4 + Vite 7 |
| RBAC | spatie/laravel-permission 6.x |
| Файлы | unisharp/laravel-filemanager |
| Тесты | PHPUnit 11.5 |
| Линтер | Laravel Pint |

## Правила зависимостей

```
Controllers → Services → Models
     ↓
  Middleware (сквозные задачи)
```

- ✅ Контроллеры вызывают сервисы
- ✅ Сервисы работают с моделями и инфраструктурой
- ❌ Контроллеры не вызывают модели напрямую
- ❌ Модели не зависят от сервисов или контроллеров

## Коммуникация

- **Контроллер → Сервис:** внедрение через конструктор, вызов метода
- **Контроллер → FormRequest:** валидация через `CheckoutRequest` (`app/Http/Requests/`)
- **Сервис → Модель:** вызов методов модели (бизнес-логика внутри модели)
- **Сервис → Инфраструктура:** Telegram, Email — через фасады или DI
- **Observer → Логирование:** ProductVariantObserver записывает PriceLog
- **Checkout flow:** `CheckoutRequest` (валидация) → `CheckoutController::store()` → `OrderService::create()` → `CartService::clear()` → редирект на `checkout.success`

## Интеграции

| Сервис | Назначение | Реализация |
|--------|-----------|------------|
| Telegram | Уведомления о заказах | `TelegramService` |
| Яндекс.Маркет | Рейтинги | Поля в моделях |
| Google Merchant | XML-фид | `FeedExportService` (в разработке) |
| TinyMCE | WYSIWYG-редактор | `moonshine/tinymce` |

## См. также

- [Начало работы](getting-started.md) — установка и запуск
- [Деплой](deployment.md) — production-развёртывание
