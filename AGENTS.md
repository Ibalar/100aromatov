# AGENTS.md

> Этот файл создан автоматически при настройке `/aif`. Обновляется при изменении структуры проекта.

## Обзор проекта
Интернет-магазин парфюмерии «100 Ароматов» на Laravel 12 с админ-панелью MoonShine 4. Каталог товаров, корзина, оформление заказа, личный кабинет, отзывы, промокоды.

## Технический стек
- **Язык:** PHP 8.2
- **Фреймворк:** Laravel 12
- **База данных:** MySQL 8.4 (основная), SQLite (тестирование)
- **ORM:** Eloquent
- **Админ-панель:** MoonShine 4.x + TinyMCE
- **Фронтенд:** Blade + Tailwind CSS 4 + Vite 7
- **RBAC:** spatie/laravel-permission 6.x

## Структура проекта
```
app/
├── Http/
│   ├── Controllers/     # 19 контроллеров (каталог, корзина, заказы, SEO...)
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
public/                  # Точка входа (index.php), статика
```

## Ключевые точки входа
| Файл | Назначение |
|------|-----------|
| `public/index.php` | Точка входа HTTP-запросов |
| `artisan` | Консольный интерфейс Laravel |
| `routes/web.php` | Все веб-маршруты |
| `config/database.php` | Настройки подключения к БД |
| `config/moonshine.php` | Настройки админ-панели |
| `config/permission.php` | Настройки Spatie Permission |
| `composer.json` | PHP-зависимости и скрипты |
| `package.json` | Фронтенд-зависимости (Vite, Tailwind) |
| `vite.config.js` | Конфигурация сборщика Vite |
| `phpunit.xml` | Конфигурация тестов |

## Документация
| Документ | Путь | Описание |
|----------|------|----------|
| README | README.md | Лендинг-страница проекта |
| Начало работы | docs/getting-started.md | Установка, настройка, первый запуск |
| Архитектура | docs/architecture.md | Структура проекта, паттерны, стек |
| Деплой | docs/deployment.md | Сборка и production-развёртывание |
| Роадмап | roadmap_do_reliza_internet_magazina.md | План работ до релиза |

## AI-контекстные файлы
| Файл | Назначение |
|------|-----------|
| AGENTS.md | Структурная карта проекта для AI-агентов |
| .ai-factory/DESCRIPTION.md | Спецификация проекта (техстек, архитектура, интеграции) |
| .ai-factory/ARCHITECTURE.md | Архитектурные решения, структура папок, правила зависимостей |
| .ai-factory/rules/base.md | Соглашения проекта (именование, стиль кода, логирование) |

## Правила для агентов
- Команды оболочки, состоящие из нескольких частей, следует разбивать на отдельные вызовы (например, `git checkout <ветка>` затем `git pull origin <ветка>`, а не `git checkout <ветка> && git pull`)
  - Неправильно: `git checkout main && git pull`
  - Правильно: сначала `git checkout main`, затем `git pull origin main`
