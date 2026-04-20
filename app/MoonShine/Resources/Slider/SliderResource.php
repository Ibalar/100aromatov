<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Slider;

use App\Models\Slider;
use App\MoonShine\Resources\Slider\Pages\SliderIndexPage;
use App\MoonShine\Resources\Slider\Pages\SliderFormPage;
use App\MoonShine\Resources\Slider\Pages\SliderDetailPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Support\Enums\SortDirection;

/**
 * @extends ModelResource<Slider, SliderIndexPage, SliderFormPage, SliderDetailPage>
 */
class SliderResource extends ModelResource
{
    protected string $model = Slider::class;

    protected string $title = 'Слайдеры';

    protected string $column = 'title_ru';

    protected string $sortColumn = 'sort_order';

    protected SortDirection $sortDirection = SortDirection::ASC;

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            SliderIndexPage::class,
            SliderFormPage::class,
            SliderDetailPage::class,
        ];
    }
}
