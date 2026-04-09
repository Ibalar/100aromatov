<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(WishlistService $wishlistService)
    {
        return view('wishlist.index', [
            'products' => $wishlistService->items(),
            'wishlistCount' => $wishlistService->count(),
        ]);
    }

    public function summary(WishlistService $wishlistService): JsonResponse
    {
        return response()->json([
            'ids' => $wishlistService->ids(),
            'count' => $wishlistService->count(),
        ]);
    }

    public function toggle(Request $request, WishlistService $wishlistService): JsonResponse
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $product = Product::where('id', $data['product_id'])
            ->where('is_active', true)
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => __('Товар недоступен'),
            ], 422);
        }

        $inWishlist = $wishlistService->toggle((int) $data['product_id']);

        return response()->json([
            'success' => true,
            'in_wishlist' => $inWishlist,
            'count' => $wishlistService->count(),
            'message' => $inWishlist
                ? __('Товар добавлен в избранное')
                : __('Товар удален из избранного'),
        ]);
    }

    public function clear(WishlistService $wishlistService): JsonResponse
    {
        $wishlistService->clear();

        return response()->json([
            'success' => true,
            'count' => 0,
            'message' => __('Избранное очищено'),
        ]);
    }
}
