<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\FeedExportProfile;

use App\Models\FeedExportProfile;
use App\MoonShine\Resources\FeedExportProfile\Pages\FeedExportProfileDetailPage;
use App\MoonShine\Resources\FeedExportProfile\Pages\FeedExportProfileFormPage;
use App\MoonShine\Resources\FeedExportProfile\Pages\FeedExportProfileIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Resources\ModelResource;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;

/**
 * @extends ModelResource<FeedExportProfile, FeedExportProfileIndexPage, FeedExportProfileFormPage, FeedExportProfileDetailPage>
 */
class FeedExportProfileResource extends ModelResource
{
    use WithRolePermissions;

    protected string $model = FeedExportProfile::class;

    protected string $title = 'Фиды товаров';

    protected string $column = 'name';

    protected int $itemsPerPage = 20;

    protected function pages(): array
    {
        return [
            FeedExportProfileIndexPage::class,
            FeedExportProfileFormPage::class,
            FeedExportProfileDetailPage::class,
        ];
    }
}
