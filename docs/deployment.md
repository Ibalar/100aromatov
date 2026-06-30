[← Архитектура](architecture.md) · [Документация](../README.md)

# Деплой

## Production-сборка

```bash
# Установка зависимостей
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Кэширование
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Миграции
php artisan migrate --force
```

## Переменные окружения (production)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://100aromatov.by

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=100aromatov
DB_USERNAME=100aromatov
DB_PASSWORD=<secure-password>

SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.100aromatov.by

LOG_CHANNEL=single
LOG_LEVEL=error

QUEUE_CONNECTION=database

TINYMCE_TOKEN=<your-token>
```

## Очереди

Проект использует database-драйвер очередей. Запуск воркера:

```bash
php artisan queue:work --tries=3 --timeout=60
```

Для production используйте Supervisor:

```ini
[program:100aromatov-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/100aromatov-queue.log
```

## Крон

```cron
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

## Проверка перед деплоем

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Сгенерирован `APP_KEY`
- [ ] Настроен `SESSION_SECURE_COOKIE=true`
- [ ] Кэши актуальны (`config:cache`, `route:cache`, `view:cache`)
- [ ] База данных накачена миграциями
- [ ] Статика собрана (`npm run build`)
- [ ] Права на `storage/` и `bootstrap/cache/`

## См. также

- [Начало работы](getting-started.md) — локальная установка
- [Архитектура](architecture.md) — структура проекта
