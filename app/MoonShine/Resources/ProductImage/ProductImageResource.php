<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductImage;

use App\Models\ProductImage;
use App\MoonShine\Resources\ProductImage\Pages\ProductImageDetailPage;
use App\MoonShine\Resources\ProductImage\Pages\ProductImageFormPage;
use App\MoonShine\Resources\ProductImage\Pages\ProductImageIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Resources\ModelResource;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

/**
 * @extends ModelResource<ProductImage, ProductImageIndexPage, ProductImageFormPage, ProductImageDetailPage>
 */
class ProductImageResource extends ModelResource
{
    use WithRolePermissions;

    protected string $model = ProductImage::class;

    protected int $itemsPerPage = 20;

    protected bool $withConfirm = true;

    protected string $title = 'ProductImages';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            ProductImageIndexPage::class,
            ProductImageFormPage::class,
            ProductImageDetailPage::class,
        ];
    }
}
