<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ProductImage;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use App\MoonShine\Resources\ProductImage\Pages\ProductImageIndexPage;
use App\MoonShine\Resources\ProductImage\Pages\ProductImageFormPage;
use App\MoonShine\Resources\ProductImage\Pages\ProductImageDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<ProductImage, ProductImageIndexPage, ProductImageFormPage, ProductImageDetailPage>
 */
class ProductImageResource extends ModelResource
{
    protected string $model = ProductImage::class;

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
