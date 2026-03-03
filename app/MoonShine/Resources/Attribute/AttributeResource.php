<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Attribute;

use Illuminate\Database\Eloquent\Model;
use App\Models\Attribute;
use App\MoonShine\Resources\Attribute\Pages\AttributeIndexPage;
use App\MoonShine\Resources\Attribute\Pages\AttributeFormPage;
use App\MoonShine\Resources\Attribute\Pages\AttributeDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Attribute, AttributeIndexPage, AttributeFormPage, AttributeDetailPage>
 */
class AttributeResource extends ModelResource
{
    protected string $model = Attribute::class;

    protected string $title = 'Характеристики';

    protected string $column = 'name_ru';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            AttributeIndexPage::class,
            AttributeFormPage::class,
            AttributeDetailPage::class,
        ];
    }
}
