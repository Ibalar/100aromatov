<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Setting;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;
use App\MoonShine\Resources\Setting\Pages\SettingIndexPage;
use App\MoonShine\Resources\Setting\Pages\SettingFormPage;
use App\MoonShine\Resources\Setting\Pages\SettingDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Setting, SettingIndexPage, SettingFormPage, SettingDetailPage>
 */
class SettingResource extends ModelResource
{
    protected string $model = Setting::class;

    protected string $title = 'Настройки';

    protected string $column = 'usd_rate';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            SettingIndexPage::class,
            SettingFormPage::class,
            SettingDetailPage::class,
        ];
    }
}
