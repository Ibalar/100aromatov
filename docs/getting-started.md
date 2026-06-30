[← Документация](../README.md) · [Архитектура →](architecture.md)

# Начало работы

## Требования

- PHP 8.2+
- MySQL 8.4
- Node.js 22+
- Composer 2.x

## Установка

```bash
git clone <repo-url> 100aromatov
cd 100aromatov
```

### 1. PHP-зависимости

```bash
composer install
```

### 2. Настройка окружения

```bash
cp .env.example .env
php artisan key:generate
```

Настройте подключение к БД в `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=mysql-8.4.local
DB_PORT=3306
DB_DATABASE=100aromatov
DB_USERNAME=100aromatov
DB_PASSWORD=your_password
```

### 3. Миграции и сиды

```bash
php artisan migrate --seed
```

### 4. Фронтенд

```bash
npm install
npm run build
```

## Запуск в разработке

```bash
composer run dev
```

Запускает параллельно:
- `php artisan serve` — HTTP-сервер
- `php artisan queue:listen` — обработка очередей
- `php artisan pail` — логи в реальном времени
- `npm run dev` — Vite dev-сервер (HMR)

## Тестирование

```bash
composer run test
```

Для локального запуска с конкретным PHP:

```bash
composer run test:local
```

База для тестов: SQLite (`database/testing.sqlite`).

## Полезные команды

| Команда | Назначение |
|---------|-----------|
| `php artisan inspire` | Мотивационная цитата |
| `php artisan tinker` | Интерактивная консоль |
| `php artisan route:list` | Список маршрутов |
| `php artisan migrate:status` | Статус миграций |

## См. также

- [Архитектура](architecture.md) — структура проекта и паттерны
- [Деплой](deployment.md) — production-развёртывание
