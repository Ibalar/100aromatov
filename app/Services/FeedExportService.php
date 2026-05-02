<?php

namespace App\Services;

use App\Models\FeedExportProfile;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FeedExportService
{
    public function generate(FeedExportProfile $profile): string
    {
        return $profile->platform === 'yandex'
            ? $this->generateYandexXml($profile)
            : $this->generateGoogleXml($profile);
    }

    private function generateGoogleXml(FeedExportProfile $profile): string
    {
        $products = $this->collectProducts($profile);
        $shopName = $this->resolveShopName($profile);
        $companyName = $this->resolveCompanyName($profile);
        $currency = $profile->currency ?: 'BYN';
        $localeField = $profile->language === 'by' ? 'by' : 'ru';
        $usdRate = (float) (Setting::getSettings()->usd_rate ?? 1);

        $items = [];

        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                if (! $variant->is_active) {
                    continue;
                }

                $priceUsd = (float) ($variant->final_price_usd ?? 0);
                $priceByn = round($priceUsd * $usdRate, 2);

                if ($profile->only_in_stock && $priceByn <= 0) {
                    continue;
                }

                $title = $this->productTitle($product, $variant, $localeField);
                $description = $this->cleanDescription((string) localizedField($product, 'description'));
                $link = route('product.show', $product->slug);
                $image = $this->productImage($product);
                $brand = trim((string) ($product->brand?->name ?? ''));
                $categoryName = trim((string) localizedField($product->category, 'name'));
                $availability = $priceByn > 0 ? 'in stock' : 'preorder';
                $offerId = $variant->sku ?: 'PRD-' . $product->id . '-' . $variant->id;

                $items[] = [
                    'id' => $offerId,
                    'title' => $title,
                    'description' => $description,
                    'link' => $link,
                    'image_link' => $image,
                    'availability' => $availability,
                    'price' => number_format($priceByn, 2, '.', '') . ' ' . $currency,
                    'brand' => $brand,
                    'condition' => 'new',
                    'product_type' => $categoryName,
                    'item_group_id' => (string) $product->id,
                ];
            }
        }

        $itemXml = collect($items)->map(function (array $item): string {
            $lines = [];
            foreach ($item as $key => $value) {
                if ($value === '' || $value === null) {
                    continue;
                }
                $tagValue = htmlspecialchars((string) $value, ENT_XML1);
                $lines[] = "      <g:{$key}>{$tagValue}</g:{$key}>";
            }

            return "    <item>\n" . implode("\n", $lines) . "\n    </item>";
        })->implode("\n");

        $title = htmlspecialchars($shopName, ENT_XML1);
        $link = htmlspecialchars(url('/'), ENT_XML1);
        $description = htmlspecialchars($companyName, ENT_XML1);

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
  <channel>
    <title>{$title}</title>
    <link>{$link}</link>
    <description>{$description}</description>
{$itemXml}
  </channel>
</rss>
XML;
    }

    private function generateYandexXml(FeedExportProfile $profile): string
    {
        $products = $this->collectProducts($profile);
        $shopName = $this->resolveShopName($profile);
        $companyName = $this->resolveCompanyName($profile);
        $currency = $profile->currency ?: 'BYN';
        $localeField = $profile->language === 'by' ? 'by' : 'ru';
        $usdRate = (float) (Setting::getSettings()->usd_rate ?? 1);

        $categories = $products
            ->pluck('category')
            ->filter()
            ->unique('id')
            ->values();

        $categoriesXml = $categories->map(function ($category): string {
            $id = (int) $category->id;
            $name = htmlspecialchars((string) localizedField($category, 'name'), ENT_XML1);

            return "      <category id=\"{$id}\">{$name}</category>";
        })->implode("\n");

        $offers = [];

        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                if (! $variant->is_active) {
                    continue;
                }

                $priceUsd = (float) ($variant->final_price_usd ?? 0);
                $priceByn = round($priceUsd * $usdRate, 2);

                if ($profile->only_in_stock && $priceByn <= 0) {
                    continue;
                }

                $offerId = htmlspecialchars((string) ($variant->sku ?: 'PRD-' . $product->id . '-' . $variant->id), ENT_XML1);
                $available = $priceByn > 0 ? 'true' : 'false';
                $url = htmlspecialchars(route('product.show', $product->slug), ENT_XML1);
                $price = number_format($priceByn, 2, '.', '');
                $categoryId = (int) ($product->category_id ?? 0);
                $picture = htmlspecialchars($this->productImage($product), ENT_XML1);
                $name = htmlspecialchars($this->productTitle($product, $variant, $localeField), ENT_XML1);
                $vendor = htmlspecialchars((string) ($product->brand?->name ?? ''), ENT_XML1);
                $description = htmlspecialchars($this->cleanDescription((string) localizedField($product, 'description')), ENT_XML1);

                $offers[] = <<<XML
      <offer id="{$offerId}" available="{$available}">
        <url>{$url}</url>
        <price>{$price}</price>
        <currencyId>{$currency}</currencyId>
        <categoryId>{$categoryId}</categoryId>
        <picture>{$picture}</picture>
        <name>{$name}</name>
        <vendor>{$vendor}</vendor>
        <description>{$description}</description>
      </offer>
XML;
            }
        }

        $date = now()->format('Y-m-d H:i');
        $title = htmlspecialchars($shopName, ENT_XML1);
        $company = htmlspecialchars($companyName, ENT_XML1);
        $siteUrl = htmlspecialchars(url('/'), ENT_XML1);
        $offersXml = implode("\n", $offers);

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<yml_catalog date="{$date}">
  <shop>
    <name>{$title}</name>
    <company>{$company}</company>
    <url>{$siteUrl}</url>
    <currencies>
      <currency id="{$currency}" rate="1"/>
    </currencies>
    <categories>
{$categoriesXml}
    </categories>
    <offers>
{$offersXml}
    </offers>
  </shop>
</yml_catalog>
XML;
    }

    private function collectProducts(FeedExportProfile $profile): Collection
    {
        $includeCategories = $this->parseList($profile->include_category_slugs);
        $excludeCategories = $this->parseList($profile->exclude_category_slugs);
        $includeBrands = $this->parseList($profile->include_brand_slugs);
        $excludeBrands = $this->parseList($profile->exclude_brand_slugs);

        $query = Product::query()
            ->with([
                'brand:id,name,slug',
                'category:id,slug,name_ru,name_by',
                'images:id,product_id,path,sort_order',
                'variants:id,product_id,sku,price_usd,sale_price_usd,is_active,volume_ml',
            ])
            ->when(
                ! $profile->include_inactive_products,
                static fn ($q) => $q->active()
            )
            ->when(
                $includeCategories !== [],
                static fn ($q) => $q->whereHas('category', static fn ($categoryQuery) => $categoryQuery->whereIn('slug', $includeCategories))
            )
            ->when(
                $excludeCategories !== [],
                static fn ($q) => $q->whereHas('category', static fn ($categoryQuery) => $categoryQuery->whereNotIn('slug', $excludeCategories))
            )
            ->when(
                $includeBrands !== [],
                static fn ($q) => $q->whereHas('brand', static fn ($brandQuery) => $brandQuery->whereIn('slug', $includeBrands))
            )
            ->when(
                $excludeBrands !== [],
                static fn ($q) => $q->whereHas('brand', static fn ($brandQuery) => $brandQuery->whereNotIn('slug', $excludeBrands))
            )
            ->orderBy('id');

        $products = $query->get();

        if ($profile->min_price_byn || $profile->max_price_byn) {
            $usdRate = (float) (Setting::getSettings()->usd_rate ?? 1);
            $min = $profile->min_price_byn ? (float) $profile->min_price_byn : null;
            $max = $profile->max_price_byn ? (float) $profile->max_price_byn : null;

            $products = $products->filter(function (Product $product) use ($usdRate, $min, $max): bool {
                $pricedVariant = $product->variants
                    ->where('is_active', true)
                    ->sortBy('final_price_usd')
                    ->first();

                if (! $pricedVariant) {
                    return false;
                }

                $priceByn = round((float) $pricedVariant->final_price_usd * $usdRate, 2);

                if ($min !== null && $priceByn < $min) {
                    return false;
                }

                if ($max !== null && $priceByn > $max) {
                    return false;
                }

                return true;
            })->values();
        }

        if ($profile->max_items !== null && $profile->max_items > 0) {
            return $products->take($profile->max_items)->values();
        }

        return $products->values();
    }

    private function productImage(Product $product): string
    {
        $path = $product->images->sortBy('sort_order')->first()?->path;

        return $path ? asset('storage/' . $path) : asset('assets/images/logo/logo.png');
    }

    private function productTitle(Product $product, $variant, string $locale): string
    {
        $name = $locale === 'by' ? (localizedField($product, 'name') ?? $product->name_ru) : (localizedField($product, 'name') ?? $product->name_ru);
        $volume = trim((string) ($variant->volume_ml ?? ''));

        if ($volume === '') {
            return trim((string) $name);
        }

        return trim((string) $name) . ' ' . $volume . ' ml';
    }

    private function cleanDescription(string $value): string
    {
        $clean = preg_replace('/\s+/u', ' ', strip_tags($value)) ?? '';
        $clean = trim($clean);

        return Str::limit($clean, 500, '...');
    }

    private function resolveShopName(FeedExportProfile $profile): string
    {
        return trim((string) ($profile->shop_name ?: config('app.name')));
    }

    private function resolveCompanyName(FeedExportProfile $profile): string
    {
        return trim((string) ($profile->company_name ?: config('app.name')));
    }

    private function parseList(mixed $raw): array
    {
        if (is_array($raw)) {
            return collect($raw)
                ->map(static fn ($value): string => trim((string) $value))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        if (! filled($raw)) {
            return [];
        }

        $parts = preg_split('/[\s,;]+/u', (string) $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return collect($parts)
            ->map(static fn (string $value): string => trim($value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
