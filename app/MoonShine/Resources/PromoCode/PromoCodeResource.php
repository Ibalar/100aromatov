<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\PromoCode;

use App\Models\PromoCode;
use App\MoonShine\Resources\PromoCode\Pages\PromoCodeDetailPage;
use App\MoonShine\Resources\PromoCode\Pages\PromoCodeFormPage;
use App\MoonShine\Resources\PromoCode\Pages\PromoCodeIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Resources\ModelResource;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

/**
 * @extends ModelResource<PromoCode, PromoCodeIndexPage, PromoCodeFormPage, PromoCodeDetailPage>
 */
class PromoCodeResource extends ModelResource
{
    use WithRolePermissions;

    protected string $model = PromoCode::class;

    protected int $itemsPerPage = 20;

    protected bool $withConfirm = true;

    protected string $title = 'Промокоды';

    protected string $column = 'code';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            PromoCodeIndexPage::class,
            PromoCodeFormPage::class,
            PromoCodeDetailPage::class,
        ];
    }
}
