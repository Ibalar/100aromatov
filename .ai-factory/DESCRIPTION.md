# 100aromatov.by — Интернет-магазин парфюмерии

## Обзор
Интернет-магазин «100 Ароматов» на Laravel 12 с админ-панелью MoonShine 4. Реализован каталог товаров с вариантами, корзина, оформление заказа, личный кабинет, отзывы, промокоды, список желаний. Проект на стадии активной разработки перед релизом.

## Технический стек
- **Язык:** PHP 8.2
- **Фреймворк:** Laravel 12
- **База данных:** MySQL 8.4 (основная), SQLite (тестирование)
- **Админ-панель:** MoonShine 4.x
- **ORM:** Eloquent (Laravel)
- **Фронтенд:** Blade + Tailwind CSS 4 + Vite 7
- **RBAC:** spatie/laravel-permission 6.x
- **Файловый менеджер:** unisharp/laravel-filemanager
- **WYSIWYG:** TinyMCE (через moonshine/tinymce)
- **Тестирование:** PHPUnit 11.5
- **Линтер:** Laravel Pint
- **Очереди:** Database driver
- **Сессии:** Database driver
- **Кэш:** Database driver

## Интеграции
- **Telegram:** уведомления о заказах
- **Яндекс.Маркет:** рейтинги
- **Google Merchant:** XML-фид (в разработке)
- **SEO:** sitemap, микроразметка Schema.org, canonical, редиректы 301

## Архитектурные особенности
- **Слой сервисов:** CartService, OrderService, TelegramService, WishlistService, FeedExportService, LanguageService, LfmImageService
- **Модели:** 23 модели Eloquent в `app/Models/`
- **MoonShine ресурсы:** 16 групп ресурсов для администрирования
- **Миграции:** 60 миграций БД
- **Blade-компоненты:** переиспользуемые компоненты для каталога, фильтров, SEO
- **Middleware:** SetLocale (мультиязычность), ForceHttpsForMoonShine
- **Наблюдатели:** ProductVariantObserver для PriceLog

## Архитектура
Подробные архитектурные решения описаны в `.ai-factory/ARCHITECTURE.md`. Паттерн: Structured Modules (Technical Layers).

## Нефункциональные требования
- **Логирование:** LOG_LEVEL=debug, канал stack
- **Обработка ошибок:** стандартный exception handler Laravel
- **Безопасность:** CSRF-защита, валидация форм, rate limiting (в плане)
- **Кэширование:** каталог, фильтры, настройки (в плане)
- **Кодировка:** UTF-8, LF, отступы 4 пробела
