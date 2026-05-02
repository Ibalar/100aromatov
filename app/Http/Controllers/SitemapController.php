<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([
            [
                'loc' => route('home'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('categories.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => route('brands.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => route('sale.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => route('contacts.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'loc' => route('reviews.index'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.7',
            ],
        ]);

        $categories = Category::query()
            ->visible()
            ->get(['slug', 'updated_at'])
            ->map(static fn (Category $category): array => [
                'loc' => route('category.show', $category->slug),
                'lastmod' => optional($category->updated_at)->toDateString() ?? now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ]);

        $brands = Brand::query()
            ->active()
            ->get(['slug', 'updated_at'])
            ->map(static fn (Brand $brand): array => [
                'loc' => route('brand.show', $brand->slug),
                'lastmod' => optional($brand->updated_at)->toDateString() ?? now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);

        $products = Product::query()
            ->active()
            ->get(['slug', 'updated_at'])
            ->map(static fn (Product $product): array => [
                'loc' => route('product.show', $product->slug),
                'lastmod' => optional($product->updated_at)->toDateString() ?? now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ]);

        $pages = Page::query()
            ->active()
            ->get(['slug', 'updated_at'])
            ->map(static fn (Page $page): array => [
                'loc' => route('pages.show', $page->slug),
                'lastmod' => optional($page->updated_at)->toDateString() ?? now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ]);

        $urls = $urls
            ->merge($categories)
            ->merge($brands)
            ->merge($products)
            ->merge($pages)
            ->values();

        $xmlItems = $urls->map(static function (array $url): string {
            $loc = htmlspecialchars((string) $url['loc'], ENT_XML1);
            $lastmod = htmlspecialchars((string) $url['lastmod'], ENT_XML1);
            $changefreq = htmlspecialchars((string) $url['changefreq'], ENT_XML1);
            $priority = htmlspecialchars((string) $url['priority'], ENT_XML1);

            return <<<XML
    <url>
        <loc>{$loc}</loc>
        <lastmod>{$lastmod}</lastmod>
        <changefreq>{$changefreq}</changefreq>
        <priority>{$priority}</priority>
    </url>
XML;
        })->implode("\n");

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{$xmlItems}
</urlset>
XML;

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
