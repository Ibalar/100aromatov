<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Wishlist;

use App\Models\Wishlist;
use App\MoonShine\Resources\Wishlist\Pages\WishlistDetailPage;
use App\MoonShine\Resources\Wishlist\Pages\WishlistFormPage;
use App\MoonShine\Resources\Wishlist\Pages\WishlistIndexPage;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\Ability;

/**
 * @extends ModelResource<Wishlist, WishlistIndexPage, WishlistFormPage, WishlistDetailPage>
 */
class WishlistResource extends ModelResource
{
    protected string $model = Wishlist::class;

    protected string $title = 'Избранное клиентов';

    protected string $column = 'id';

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            WishlistIndexPage::class,
            WishlistFormPage::class,
            WishlistDetailPage::class,
        ];
    }

    protected function isCan(Ability $ability): bool
    {
        if (\in_array($ability, [Ability::CREATE, Ability::UPDATE, Ability::DELETE, Ability::MASS_DELETE], true)) {
            return false;
        }

        return parent::isCan($ability);
    }
}
