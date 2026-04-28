<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\FilterPage;

use App\Models\FilterPage;
use App\MoonShine\Resources\FilterPage\Pages\FilterPageDetailPage;
use App\MoonShine\Resources\FilterPage\Pages\FilterPageFormPage;
use App\MoonShine\Resources\FilterPage\Pages\FilterPageIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Resources\ModelResource;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

/**
 * @extends ModelResource<FilterPage, FilterPageIndexPage, FilterPageFormPage, FilterPageDetailPage>
 */
class FilterPageResource extends ModelResource
{
    use WithRolePermissions;

    protected string $model = FilterPage::class;

    protected int $itemsPerPage = 20;

    protected bool $withConfirm = true;

    protected string $title = 'Страницы для фильтра';

    protected string $column = 'h1_ru';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            FilterPageIndexPage::class,
            FilterPageFormPage::class,
            FilterPageDetailPage::class,
        ];
    }
}
