# План: Блок «С этим покупают»

> **Дата:** 2026-06-30
> **Режим:** Fast

## Roadmap Linkage

- Milestone: "2.3 Блок «С этим покупают»"
- Rationale: Связь related_products + вывод в карточке товара

## Settings

- **Testing:** yes
- **Logging:** verbose
- **Docs:** yes

## Задачи

- [ ] **T1** — Миграция: pivot-таблица `product_related`
  - `product_id` FK, `related_product_id` FK, unique constraint
  - **Файл:** `database/migrations/..._create_product_related_table.php`

- [ ] **T2** — Отношение в модели Product
  - `belongsToMany` на себя через `product_related`
  - **Файл:** `app/Models/Product.php`

- [ ] **T3** — Вывод на странице товара
  - Блок «С этим покупают» после характеристик (как recently-viewed)
  - **Файл:** `resources/views/products/show.blade.php`

- [ ] **T4** — Админка: управление связями в MoonShine
  - `BelongsToMany` поле в ProductFormPage
  - **Файл:** `app/MoonShine/Resources/Product/Pages/ProductFormPage.php`

- [ ] **T5** — Тесты
  - **Файл:** `tests/Feature/RelatedProductsTest.php`
