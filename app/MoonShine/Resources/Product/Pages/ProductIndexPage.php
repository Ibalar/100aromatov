<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Product\Pages;

use App\Models\Brand;
use App\Models\Product;
use App\MoonShine\Resources\Product\ProductResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * @extends IndexPage<ProductResource>
 */
class ProductIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        $resource = $this->getResource();

        return [
            ID::make()->sortable(),
            Text::make('Название', 'name_ru')
                ->link(
                    static fn (string $value, Text $field) => $resource->getFormPageUrl(
                        $field->getData()?->getKey()
                    ),
                    blank: false
                )
                ->withoutTextWrap()
                ->sortable(),
        ];
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    /**
     * @return list<FieldContract>
     */
    protected function filters(): iterable
    {
        return [
            Select::make('Бренд', 'brand_id')
                ->options(
                    Cache::remember('brand_options', 3600, fn() => Brand::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray())
                )
                ->native()
                ->nullable()
                ->placeholder('Все бренды'),

            Switcher::make('Активен', 'is_active'),
        ];
    }

    /**
     * @return list<QueryTag>
     */
    protected function queryTags(): array
    {
        return [];
    }

    /**
     * @return list<Metric>
     */
    protected function metrics(): array
    {
        return [];
    }

    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyListComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    protected function modifyDetailButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button
            ->setUrl(
                static fn (?Product $product): string => route('product.show', $product?->slug)
            )
            ->onAfterSet(
                static function (?DataWrapperContract $data, ActionButtonContract $ctx): void {
                    $key = $data?->getKey();

                    if ($key !== null) {
                        $ctx->customAttributes([
                            'data-highlight-row-key' => (string) $key,
                        ]);
                    }
                }
            )
            ->blank()
            ->disableAsync();
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [
            ...parent::topLayer(),
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer(),
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer(),
            FlexibleRender::make($this->rowHighlightAssets()),
        ];
    }

    private function rowHighlightAssets(): string
    {
        return <<<'HTML'
<style>
    tr.product-index-detail-highlight > td {
        background: rgba(245, 158, 11, 0.16) !important;
    }

    tr.product-index-detail-highlight > td:first-child {
        box-shadow: inset 4px 0 0 #f59e0b;
    }
</style>
<script>
    (function () {
        var storageKey = 'moonshine:products:index:last-detail-row-key';
        var rowClass = 'product-index-detail-highlight';
        var buttonSelector = '.js-detail-button[data-highlight-row-key]';

        function getStoredKey() {
            try {
                return sessionStorage.getItem(storageKey);
            } catch (e) {
                return null;
            }
        }

        function setStoredKey(value) {
            try {
                sessionStorage.setItem(storageKey, value);
            } catch (e) {
                // ignore storage errors
            }
        }

        function clearHighlight() {
            document.querySelectorAll('tr.' + rowClass).forEach(function (row) {
                row.classList.remove(rowClass);
            });
        }

        function applyHighlight(rowKey) {
            clearHighlight();

            if (!rowKey) {
                return;
            }

            var safeKey = (window.CSS && window.CSS.escape) ? window.CSS.escape(rowKey) : rowKey;
            var row = document.querySelector('tr[data-row-key="' + safeKey + '"]');

            if (row) {
                row.classList.add(rowClass);
            }
        }

        document.addEventListener('click', function (event) {
            var button = event.target.closest(buttonSelector);
            if (!button) {
                return;
            }

            var rowKey = button.getAttribute('data-highlight-row-key');
            if (!rowKey) {
                return;
            }

            setStoredKey(rowKey);
            applyHighlight(rowKey);
        });

        var observer = new MutationObserver(function () {
            applyHighlight(getStoredKey());
        });

        observer.observe(document.body, { childList: true, subtree: true });

        applyHighlight(getStoredKey());
    })();
</script>
HTML;
    }
}
