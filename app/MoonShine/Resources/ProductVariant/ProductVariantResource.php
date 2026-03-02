<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductVariant;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;
use App\MoonShine\Resources\ProductVariant\Pages\ProductVariantIndexPage;
use App\MoonShine\Resources\ProductVariant\Pages\ProductVariantFormPage;
use App\MoonShine\Resources\ProductVariant\Pages\ProductVariantDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<ProductVariant, ProductVariantIndexPage, ProductVariantFormPage, ProductVariantDetailPage>
 */
class ProductVariantResource extends ModelResource
{
    protected string $model = ProductVariant::class;

    protected string $title = 'Варианты товара';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            ProductVariantIndexPage::class,
            ProductVariantFormPage::class,
            ProductVariantDetailPage::class,
        ];
    }
}
