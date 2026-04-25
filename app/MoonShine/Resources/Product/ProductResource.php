<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\MoonShine\Resources\Product\Pages\ProductDetailPage;
use App\MoonShine\Resources\Product\Pages\ProductFormPage;
use App\MoonShine\Resources\Product\Pages\ProductIndexPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Crud\Handlers\Handler;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\ImportHandler;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Product, ProductIndexPage, ProductFormPage, ProductDetailPage>
 */
class ProductResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected string $model = Product::class;
    
    protected int $itemsPerPage = 20;

    protected bool $withConfirm = true;

    protected bool $saveQueryState = true;

    protected string $title = 'Товары';

    protected string $column = 'name_ru';

    protected array $pendingImages = [];

    protected array $pendingVariants = [];

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            ProductIndexPage::class,
            ProductFormPage::class,
            ProductDetailPage::class,
        ];
    }

    protected function isExportToCsv(): bool
    {
        return true;
    }

    protected function import(): ?Handler
    {
        return ImportHandler::make(__('moonshine::ui.import'))
            ->delimiter(',');
    }

    /**
     * @return list<FieldContract>
     */
    protected function importFields(): iterable
    {
        return [
            ID::make('ID', 'id'),
            Text::make('name_ru', 'name_ru'),
            Text::make('name_by', 'name_by'),
            Text::make('Slug', 'slug'),
            Text::make('old_url', 'old_url'),
            Text::make('gender', 'gender'),
            Text::make('gender_by', 'gender_by'),
            Text::make('concentration', 'concentration'),
            Text::make('concentration_by', 'concentration_by'),
            Text::make('country', 'country'),
            Text::make('country_by', 'country_by'),
            Text::make('description_ru', 'description_ru'),
            Text::make('description_by', 'description_by'),
            Text::make('sku', 'sku'),
            Text::make('volume_ml', 'volume_ml')->fromRaw(
                static fn (mixed $raw) => blank($raw) ? null : trim((string) $raw)
            ),
            Text::make('price_usd', 'price_usd')->fromRaw(
                static fn (mixed $raw) => blank($raw) ? null : (float) $raw
            ),
            Text::make('variants', 'variants'),
            Text::make('is_active', 'is_active')->fromRaw(
                static fn (mixed $raw) => ProductResource::toBoolean($raw)
            ),
            Text::make('is_featured', 'is_featured')->fromRaw(
                static fn (mixed $raw) => ProductResource::toBoolean($raw)
            ),
            Text::make('brand', 'brand_id')->fromRaw(
                static fn (mixed $raw) => ProductResource::resolveRelationId($raw, Brand::query(), 'name')
            ),
            Text::make('category', 'category_id')->fromRaw(
                static fn (mixed $raw) => ProductResource::resolveRelationId($raw, Category::query(), 'name_ru')
            ),
            Text::make('images', 'images'),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function exportFields(): iterable
    {
        return [
            ID::make('ID', 'id'),
            Text::make('name_ru', 'name_ru'),
            Text::make(
                'variants',
                formatted: static fn (Product $product) => $product->variants()
                    ->orderByDesc('is_active')
                    ->orderBy('id')
                    ->get(['sku', 'volume_ml', 'price_usd', 'sale_price_usd'])
                    ->map(static fn (ProductVariant $variant): array => [
                        'sku' => $variant->sku,
                        'volume_ml' => $variant->volume_ml,
                        'price_usd' => $variant->price_usd,
                        'sale_price_usd' => $variant->sale_price_usd,
                    ])
                    ->values()
                    ->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ),
        ];
    }

    public function beforeImportFilling(array $data): array
    {
        if (blank($data['name_by'] ?? null) && ! blank($data['name_ru'] ?? null)) {
            $data['name_by'] = $data['name_ru'];
        }

        if (blank($data['slug'] ?? null) && ! blank($data['name_ru'] ?? null)) {
            $data['slug'] = Str::slug((string) $data['name_ru']);
        }

        if (array_key_exists('old_url', $data) && ! blank($data['old_url'])) {
            $data['old_url'] = $this->normalizeOldUrl((string) $data['old_url']);
        }

        if (array_key_exists('images', $data)) {
            $importKey = $this->getImportImageKey($data);

            if ($importKey !== null && ! blank($data['images'])) {
                $this->pendingImages[$importKey] = (string) $data['images'];
            }

            unset($data['images']);
        }

        if (
            array_key_exists('sku', $data)
            || array_key_exists('price_usd', $data)
            || array_key_exists('volume_ml', $data)
            || array_key_exists('variants', $data)
        ) {
            $importKey = $this->getImportImageKey($data);

            if ($importKey !== null) {
                $variants = [];

                $singleVariant = [
                    'sku' => $data['sku'] ?? null,
                    'volume_ml' => $data['volume_ml'] ?? null,
                    'price_usd' => $data['price_usd'] ?? null,
                ];

                if (
                    ! blank($singleVariant['sku'] ?? null)
                    || ! blank($singleVariant['volume_ml'] ?? null)
                    || ! blank($singleVariant['price_usd'] ?? null)
                ) {
                    $variants[] = $singleVariant;
                }

                $variants = [
                    ...$variants,
                    ...$this->parseVariantsColumn($data['variants'] ?? null),
                ];

                if ($variants !== []) {
                    $this->pendingVariants[$importKey] = $variants;
                }
            }

            unset($data['sku'], $data['volume_ml'], $data['price_usd'], $data['variants']);
        }

        return $data;
    }

    public function beforeImported(mixed $item): mixed
    {
        if (! $item instanceof Product) {
            return $item;
        }

        if (blank($item->name_by) && ! blank($item->name_ru)) {
            $item->name_by = $item->name_ru;
        }

        return $item;
    }

    public function afterImported(mixed $item): mixed
    {
        if (! $item instanceof Product) {
            return $item;
        }

        $importKey = $this->getImportImageKey([
            'id' => $item->id,
            'slug' => $item->slug,
        ]);

        if ($importKey === null || ! isset($this->pendingImages[$importKey])) {
            $this->importVariant($item, $importKey);

            return $item;
        }

        $sources = collect(explode('|', $this->pendingImages[$importKey]))
            ->map(static fn (string $source) => trim($source))
            ->filter()
            ->values();

        unset($this->pendingImages[$importKey]);

        if ($sources->isEmpty()) {
            return $item;
        }

        $importedPaths = [];

        foreach ($sources as $index => $source) {
            $path = $this->importImage($item, $source);

            if ($path !== null) {
                $importedPaths[] = [
                    'path' => $path,
                    'sort_order' => $index,
                ];
            }
        }

        if ($importedPaths === []) {
            return $item;
        }

        $item->images()->get()->each(function (ProductImage $image): void {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        });

        foreach ($importedPaths as $imageData) {
            $item->images()->create([
                'path' => $imageData['path'],
                'sort_order' => $imageData['sort_order'],
                'alt_ru' => $item->name_ru,
                'alt_by' => $item->name_by,
            ]);
        }

        $this->importVariant($item, $importKey);

        return $item;
    }

    protected function importVariant(Product $product, ?string $importKey): void
    {
        if ($importKey === null || ! isset($this->pendingVariants[$importKey])) {
            return;
        }

        $variantRows = $this->pendingVariants[$importKey];

        unset($this->pendingVariants[$importKey]);

        if (! is_array($variantRows)) {
            return;
        }

        foreach ($variantRows as $variantData) {
            if (! is_array($variantData)) {
                continue;
            }

            $sku = blank($variantData['sku'] ?? null) ? null : trim((string) $variantData['sku']);
            $volume = blank($variantData['volume_ml'] ?? null) ? null : (string) $variantData['volume_ml'];
            $price = blank($variantData['price_usd'] ?? null) ? null : (float) $variantData['price_usd'];

            if ($sku === null && $volume === null && $price === null) {
                continue;
            }

            // Compatibility requirement: variant with SKU equal to product id must be skipped.
            if ($sku !== null && $sku === (string) $product->id) {
                continue;
            }

            $variant = null;

            if ($sku !== null) {
                $variant = ProductVariant::query()->where('sku', $sku)->first();

                // SKU is globally unique. If it belongs to another product, skip this row.
                if ($variant instanceof ProductVariant && (int) $variant->product_id !== (int) $product->id) {
                    Log::warning('Product variant import skipped due to SKU conflict', [
                        'product_id' => $product->id,
                        'sku' => $sku,
                        'existing_variant_id' => $variant->id,
                        'existing_product_id' => $variant->product_id,
                    ]);

                    continue;
                }
            }

            if (! $variant instanceof ProductVariant) {
                $variant = $product->variants()->make([
                    'is_active' => true,
                ]);
            }

            if ($sku !== null) {
                $variant->sku = $sku;
            }

            if ($price !== null) {
                $variant->price_usd = $price;
            }

            if ($volume !== null) {
                $variant->volume_ml = $volume;
            }

            if (! $variant->exists && blank($variant->sku)) {
                continue;
            }

            if (! $variant->exists && blank($variant->price_usd)) {
                continue;
            }

            $variant->is_active = true;
            $variant->product()->associate($product);

            try {
                $variant->save();
            } catch (QueryException $e) {
                // Defensive fallback: do not fail the whole import because of one conflicting row.
                Log::warning('Product variant import save failed', [
                    'product_id' => $product->id,
                    'sku' => $sku,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * @return list<array{sku: string|null, volume_ml: string|int|float|null, price_usd: float|int|string|null}>
     */
    protected function parseVariantsColumn(mixed $raw): array
    {
        if (blank($raw)) {
            return [];
        }

        $rawString = trim((string) $raw);
        if ($rawString === '') {
            return [];
        }

        // Preferred format: JSON array of objects.
        $decoded = json_decode($rawString, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $rows = array_is_list($decoded) ? $decoded : [$decoded];

            return collect($rows)
                ->filter(static fn (mixed $row): bool => is_array($row))
                ->map(static function (array $row): array {
                    return [
                        'sku' => blank($row['sku'] ?? null) ? null : trim((string) $row['sku']),
                        'volume_ml' => blank($row['volume_ml'] ?? null) ? null : (string) $row['volume_ml'],
                        'price_usd' => blank($row['price_usd'] ?? null) ? null : (float) $row['price_usd'],
                    ];
                })
                ->values()
                ->all();
        }

        // Backward-compatible format from legacy dumps:
        // [{sku:1248,volume_ml:100,price_usd:0.00|sku:3129,volume_ml:30,price_usd:0.00}]
        $normalized = trim($rawString);
        $normalized = preg_replace('/^\[\{?/', '', $normalized) ?? $normalized;
        $normalized = preg_replace('/\}?\]$/', '', $normalized) ?? $normalized;

        if ($normalized === '') {
            return [];
        }

        $chunks = preg_split('/\|(?=sku:)/', $normalized) ?: [];

        return collect($chunks)
            ->map(static fn (string $chunk): string => trim($chunk))
            ->filter()
            ->map(static function (string $chunk): ?array {
                $pattern = '/^sku:(.*?),volume_ml:(.*),price_usd:(.*)$/u';

                if (! preg_match($pattern, $chunk, $matches)) {
                    return null;
                }

                $sku = trim((string) $matches[1]);
                $volume = trim((string) $matches[2]);
                $priceRaw = trim((string) $matches[3]);
                $priceNormalized = str_replace(',', '.', $priceRaw);

                return [
                    'sku' => $sku === '' ? null : $sku,
                    'volume_ml' => $volume === '' ? null : $volume,
                    'price_usd' => $priceNormalized === '' ? null : (float) $priceNormalized,
                ];
            })
            ->filter(static fn (?array $row): bool => is_array($row))
            ->values()
            ->all();
    }

    protected function getImportImageKey(array $data): ?string
    {
        if (! empty($data['id'])) {
            return 'id:' . $data['id'];
        }

        if (! empty($data['slug'])) {
            return 'slug:' . $data['slug'];
        }

        return null;
    }

    protected static function resolveRelationId(mixed $raw, Builder $query, string $nameColumn): ?int
    {
        if (blank($raw)) {
            return null;
        }

        if (is_numeric($raw)) {
            return (int) $raw;
        }

        return $query
            ->where('slug', $raw)
            ->orWhere($nameColumn, $raw)
            ->value('id');
    }

    protected static function toBoolean(mixed $raw): bool
    {
        return filter_var($raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ?? (int) $raw === 1;
    }

    protected function normalizeOldUrl(string $oldUrl): string
    {
        $path = parse_url($oldUrl, PHP_URL_PATH) ?: $oldUrl;

        return '/' . ltrim($path, '/');
    }

    protected function importImage(Product $product, string $source): ?string
    {
        try {
            if (Str::startsWith($source, ['http://', 'https://'])) {
                return $this->importRemoteImage($product, $source);
            }

            return $this->importLocalImage($product, $source);
        } catch (\Throwable $e) {
            Log::warning('Product image import failed', [
                'product_id' => $product->id,
                'source' => $source,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function importRemoteImage(Product $product, string $source): ?string
    {
        $response = Http::timeout(60)->get($source);

        if (! $response->successful()) {
            return null;
        }

        $extension = pathinfo((string) parse_url($source, PHP_URL_PATH), PATHINFO_EXTENSION);
        $extension = $extension !== '' ? $extension : 'jpg';

        $relativePath = sprintf(
            'products/%d/%s.%s',
            $product->id,
            (string) Str::uuid(),
            strtolower($extension)
        );

        Storage::disk('public')->put($relativePath, $response->body());

        return $relativePath;
    }

    protected function importLocalImage(Product $product, string $source): ?string
    {
        $normalizedSource = ltrim(str_replace('\\', '/', $source), '/');

        $candidates = [
            storage_path('app/imports/' . $normalizedSource),
            storage_path('app/public/' . $normalizedSource),
            public_path($normalizedSource),
        ];

        foreach ($candidates as $candidate) {
            if (! is_file($candidate)) {
                continue;
            }

            $extension = pathinfo($candidate, PATHINFO_EXTENSION) ?: 'jpg';
            $relativePath = sprintf(
                'products/%d/%s.%s',
                $product->id,
                (string) Str::uuid(),
                strtolower($extension)
            );

            Storage::disk('public')->put($relativePath, file_get_contents($candidate));

            return $relativePath;
        }

        return null;
    }
}
