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
                static fn (mixed $raw) => blank($raw) ? null : (int) $raw
            ),
            Text::make('price_usd', 'price_usd')->fromRaw(
                static fn (mixed $raw) => blank($raw) ? null : (float) $raw
            ),
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
            Text::make('name_by', 'name_by'),
            Text::make('Slug', 'slug'),
            Text::make('gender', 'gender'),
            Text::make('gender_by', 'gender_by'),
            Text::make('concentration', 'concentration'),
            Text::make('concentration_by', 'concentration_by'),
            Text::make('country', 'country'),
            Text::make('country_by', 'country_by'),
            Text::make(
                'images',
                formatted: static fn (Product $product) => $product->images()
                    ->orderBy('sort_order')
                    ->get()
                    ->map(static fn (ProductImage $image) => asset('storage/' . $image->path))
                    ->implode('|')
            ),
            Text::make('description_ru', 'description_ru'),
            Text::make('description_by', 'description_by'),
            Text::make('sku', formatted: static fn (Product $product) => $product->variants()
                ->where('is_active', true)
                ->orderBy('id')
                ->value('sku')),
            Text::make('volume_ml', formatted: static fn (Product $product) => $product->variants()
                ->where('is_active', true)
                ->orderBy('id')
                ->value('volume_ml')),
            Text::make('price_usd', formatted: static fn (Product $product) => $product->variants()
                ->where('is_active', true)
                ->orderBy('id')
                ->value('price_usd')),
            Text::make('is_active', formatted: static fn (Product $product) => (int) $product->is_active),
            Text::make('is_featured', formatted: static fn (Product $product) => (int) $product->is_featured),
            Text::make('brand', formatted: static fn (Product $product) => $product->brand_id),
            Text::make('category', formatted: static fn (Product $product) => $product->category_id),
            Text::make('old_url', 'old_url'),
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

        if (array_key_exists('sku', $data) || array_key_exists('price_usd', $data) || array_key_exists('volume_ml', $data)) {
            $importKey = $this->getImportImageKey($data);

            if ($importKey !== null) {
                $this->pendingVariants[$importKey] = [
                    'sku' => $data['sku'] ?? null,
                    'volume_ml' => $data['volume_ml'] ?? null,
                    'price_usd' => $data['price_usd'] ?? null,
                ];
            }

            unset($data['sku'], $data['volume_ml'], $data['price_usd']);
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

        $variantData = $this->pendingVariants[$importKey];

        unset($this->pendingVariants[$importKey]);

        $sku = blank($variantData['sku'] ?? null) ? null : (string) $variantData['sku'];
        $volume = $variantData['volume_ml'] ?? null;
        $price = $variantData['price_usd'] ?? null;

        if ($sku === null && $volume === null && $price === null) {
            return;
        }

        $variant = null;

        if ($sku !== null) {
            $variant = $product->variants()->where('sku', $sku)->first();
        }

        if (! $variant instanceof ProductVariant) {
            $variant = $product->variants()->where('is_active', true)->orderBy('id')->first();
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
            return;
        }

        if (! $variant->exists && blank($variant->price_usd)) {
            return;
        }

        $variant->is_active = true;
        $variant->product()->associate($product);
        $variant->save();
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
