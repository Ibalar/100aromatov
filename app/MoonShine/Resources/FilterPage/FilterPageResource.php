<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\FilterPage;

use Illuminate\Database\Eloquent\Model;
use App\Models\FilterPage;
use App\MoonShine\Resources\FilterPage\Pages\FilterPageIndexPage;
use App\MoonShine\Resources\FilterPage\Pages\FilterPageFormPage;
use App\MoonShine\Resources\FilterPage\Pages\FilterPageDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<FilterPage, FilterPageIndexPage, FilterPageFormPage, FilterPageDetailPage>
 */
class FilterPageResource extends ModelResource
{
    protected string $model = FilterPage::class;

    protected string $title = 'FilterPages';
    
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
