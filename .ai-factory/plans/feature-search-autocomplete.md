# План: Поиск с автодополнением

> **Ветка:** feature/search-autocomplete
> **Дата:** 2026-06-30
> **Описание:** AJAX-автодополнение в строке поиска: endpoint, debounce на фронте, поиск по названию/SKU/бренду.

## Roadmap Linkage

- Milestone: "2.1 Поиск с автодополнением"
- Rationale: Базовая страница поиска уже есть. Нужен AJAX endpoint для быстрых подсказок при вводе с debounce на фронте.

## Settings

- **Testing:** yes
- **Logging:** verbose
- **Docs:** yes (mandatory checkpoint)

## Research Context

> **Active Summary:** GET /search уже реализован с LIKE-поиском и relevance scoring. Есть индексы: products_active_name_ru_idx, products_active_name_by_idx, product_variants_active_sku_idx, brands_active_name_idx. AJAX и JS для автодополнения отсутствуют. Поисковая строка есть в header, mobile-menu, toolbar.

---

## Задачи

### Фаза 1: AJAX Endpoint

- [x] **T1** — Создать метод `suggest()` в `ProductController`
  - `GET /search/suggest?q=...` — возвращает JSON (макс 8 результатов)
  - Поиск по `name_ru`, `name_by`, `sku` (product_variants), `brand.name`
  - LIKE с приоритетом: точное совпадение > начало строки > содержит
  - Ответ: `[{id, name, slug, brand, sku, price_byn, image}]`
  - **Файлы:** `routes/web.php`, `app/Http/Controllers/ProductController.php`
  - **Логи:** `Log::debug` с query и кол-вом результатов

### Фаза 2: Фронтенд

- [x] **T2** — Создать JS-модуль для автодополнения
  - `resources/js/search-autocomplete.js`
  - Debounce 300ms на ввод
  - Минимум 2 символа для запроса
  - Выпадающий список под строкой поиска
  - Закрытие по клику вне, по Escape
  - Выделение совпадающего текста жирным
  - Импорт в `resources/js/app.js`
  - **Логи:** `console.debug` в dev-режиме

- [x] **T3** — Интегрировать автодополнение во все строки поиска
  - `header.blade.php` — десктопная строка
  - `mobile-menu.blade.php` — мобильное меню
  - `products/search.blade.php` — страница результатов
  - **Файлы:** 3 blade-шаблона

### Фаза 3: Тесты

- [x] **T4** — Тесты suggest endpoint
  - Возвращает результаты по полному совпадению названия
  - Возвращает результаты по частичному совпадению
  - Возвращает результаты по SKU
  - Возвращает результаты по бренду
  - Пустой запрос возвращает []
  - Короткий запрос (<2 символов) возвращает []
  - Максимум 8 результатов
  - **Файл:** `tests/Feature/SearchSuggestTest.php`

### Фаза 4: Документация

- [x] **T5** — Документация (не требуется)

## Commit Plan

| # | Коммит | Задачи |
|---|--------|--------|
| 1 | `feat(search): add AJAX suggest endpoint` | T1 |
| 2 | `feat(search): add autocomplete dropdown with debounce` | T2, T3 |
| 3 | `test(search): add suggest endpoint tests` | T4 |
