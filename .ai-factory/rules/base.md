# Базовые правила проекта

> Автоопределённые соглашения на основе анализа кодовой базы. Редактируйте при необходимости.

## Соглашения об именовании

- **Файлы:** PascalCase для классов (`ProductController.php`), snake_case для конфигов и представлений (`app.blade.php`)
- **Классы:** PascalCase (`CartService`, `OrderController`)
- **Методы:** camelCase (`getCartItems`, `calculateTotal`)
- **Переменные:** camelCase (`$cartItems`, `$totalPrice`)
- **Таблицы БД:** snake_case, множественное число (`products`, `order_items`)
- **Маршруты:** kebab-case в URL, camelCase в именах (`products.show`)

## Структура модулей

- `app/Models/` — модели Eloquent (23 модели)
- `app/Http/Controllers/` — контроллеры (19 контроллеров)
- `app/Services/` — сервисный слой (7 сервисов)
- `app/MoonShine/Resources/` — CRUD-ресурсы админ-панели (16 групп)
- `app/MoonShine/Pages/` — страницы админ-панели
- `app/Http/Middleware/` — middleware
- `app/Observers/` — наблюдатели Eloquent
- `app/Providers/` — сервис-провайдеры
- `app/Support/` — хелперы
- `database/migrations/` — миграции (60 файлов)
- `resources/views/` — Blade-шаблоны (~55 файлов)
- `routes/` — маршруты (web.php, console.php)

## Обработка ошибок

- Стандартный обработчик исключений Laravel (`App\Exceptions\Handler`)
- В сервисах — выброс исключений с понятными сообщениями
- Валидация через FormRequest (в плане — стандартизация)

## Логирование

- Фасад `Log` Laravel
- Канал `stack` (в разработке), `single` на проде
- Уровень: `debug` (local), `error` и выше (production)

## Тестирование

- PHPUnit 11.5
- Конфигурация: `phpunit.xml`
- Запуск: `composer run test`
- База для тестов: SQLite (`database/testing.sqlite`)

## Стиль кода

- Кодировка: UTF-8
- Перевод строк: LF
- Отступы: 4 пробела
- Линтер: Laravel Pint
- Финальный перевод строки: обязателен
