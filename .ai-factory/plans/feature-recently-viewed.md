# План: Недавно просмотренные

> **Ветка:** feature/recently-viewed
> **Дата:** 2026-06-30
> **Описание:** Cookie-блок «Недавно просмотренные» на карточке товара. До 12 товаров, без дублей, новый в начало.

## Roadmap Linkage

- Milestone: "2.2 Недавно просмотренные"
- Rationale: Повышает конверсию — пользователь видит историю просмотров и возвращается к товарам.

## Settings

- **Testing:** yes
- **Logging:** verbose
- **Docs:** yes (mandatory checkpoint)

## Задачи

- [x] **T1** — Cookie-логика в `ProductController::show()`
  - При просмотре товара сохранять `product_id` в cookie `recently_viewed` (JSON-массив, макс 12)
  - Новый товар в начало, дубли удаляются, лишние обрезаются
  - Cookie на 30 дней, `httpOnly: false` (доступен из JS если нужно)
  - **Файл:** `app/Http/Controllers/ProductController.php` (изменить)
  - **Логи:** `Log::debug` при добавлении

- [x] **T2** — Блок «Недавно просмотренные» на карточке товара
  - Blade-компонент `resources/views/components/recently-viewed.blade.php`
  - Читает cookie, загружает товары через `Product::findMany()`
  - Горизонтальная прокрутка карточек (как `product-card`)
  - Показывается только если есть товары
  - **Файлы:** компонент + `resources/views/products/show.blade.php` (изменить)
  - **Логи:** `Log::debug` если cookie пуст или товары не найдены

- [x] **T3** — Тесты
  - Cookie записывается при просмотре товара
  - Дубли не добавляются
  - Максимум 12 товаров
  - Новый товар в начало списка
  - Блок отображается на странице товара
  - **Файл:** `tests/Feature/RecentlyViewedTest.php`

## Commit Plan

| # | Коммит | Задачи |
|---|--------|--------|
| 1 | `feat(product): add recently viewed cookie storage` | T1 |
| 2 | `feat(product): add recently viewed block on product page` | T2 |
| 3 | `test(product): add recently viewed tests` | T3 |
