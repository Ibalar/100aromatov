<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Wishlist;

use Illuminate\Database\Eloquent\Model;
use App\Models\Wishlist;
use App\MoonShine\Resources\Wishlist\Pages\WishlistIndexPage;
use App\MoonShine\Resources\Wishlist\Pages\WishlistFormPage;
use App\MoonShine\Resources\Wishlist\Pages\WishlistDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Wishlist, WishlistIndexPage, WishlistFormPage, WishlistDetailPage>
 */
class WishlistResource extends ModelResource
{
    protected string $model = Wishlist::class;

    protected string $title = 'Wishlists';
    
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
}
