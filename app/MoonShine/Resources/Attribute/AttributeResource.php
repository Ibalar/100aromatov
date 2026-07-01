<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Attribute;

use App\Models\Attribute;
use App\MoonShine\Resources\Attribute\Pages\AttributeDetailPage;
use App\MoonShine\Resources\Attribute\Pages\AttributeFormPage;
use App\MoonShine\Resources\Attribute\Pages\AttributeIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Resources\ModelResource;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

/**
 * @extends ModelResource<Attribute, AttributeIndexPage, AttributeFormPage, AttributeDetailPage>
 */
class AttributeResource extends ModelResource
{
    use WithRolePermissions;

    protected string $model = Attribute::class;

    protected int $itemsPerPage = 20;

    protected bool $withConfirm = true;

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
