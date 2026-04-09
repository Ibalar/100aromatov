<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(CartService $cartService)
    {
        $summary = $cartService->getSummary();

        return view('cart.index', [
            'items' => $summary['items'],
            'totalQty' => $summary['total_qty'],
            'totalByn' => $summary['total_byn'],
        ]);
    }

    public function summary(CartService $cartService): JsonResponse
    {
        $summary = $cartService->getSummary();

        return response()->json([
            'items' => $summary['items']->values()->all(),
            'count' => $summary['total_qty'],
            'total_byn' => $summary['total_byn'],
            'total_byn_formatted' => number_format($summary['total_byn'], 2, ',', ' ') . ' BYN',
            'items_html' => view('partials.cart.items', ['items' => $summary['items']])->render(),
        ]);
    }

    public function add(Request $request, CartService $cartService): JsonResponse
    {
        $data = $request->validate([
            'variant_id' => 'required|integer|exists:product_variants,id',
            'qty' => 'nullable|integer|min:1|max:99',
        ]);

        $variant = ProductVariant::where('id', $data['variant_id'])
            ->where('is_active', true)
            ->first();

        if (! $variant) {
            return response()->json([
                'success' => false,
                'message' => __('Выбранный вариант недоступен'),
            ], 422);
        }

        $cartService->add((int) $data['variant_id'], (int) ($data['qty'] ?? 1));

        return response()->json([
            'success' => true,
            'message' => __('Товар добавлен в список для бронирования'),
        ]);
    }

    public function update(Request $request, CartService $cartService): JsonResponse
    {
        $data = $request->validate([
            'variant_id' => 'required|integer',
            'qty' => 'required|integer|min:0|max:99',
        ]);

        $cartService->setQty((int) $data['variant_id'], (int) $data['qty']);

        return response()->json(['success' => true]);
    }

    public function remove(Request $request, CartService $cartService): JsonResponse
    {
        $data = $request->validate([
            'variant_id' => 'required|integer',
        ]);

        $cartService->remove((int) $data['variant_id']);

        return response()->json(['success' => true]);
    }

    public function clear(CartService $cartService): JsonResponse
    {
        $cartService->clear();

        return response()->json(['success' => true]);
    }
}
