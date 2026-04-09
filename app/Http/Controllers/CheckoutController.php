<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\CartService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(CartService $cartService)
    {
        $summary = $cartService->getSummary();

        return view('checkout.index', [
            'items' => $summary['items'],
            'totalQty' => $summary['total_qty'],
            'totalByn' => $summary['total_byn'],
        ]);
    }

    public function store(Request $request, OrderService $service, CartService $cartService)
    {
        $data = $request->validate([
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'promo_code' => 'nullable|string|max:64',
        ]);

        $items = $request->input('items');
        if (empty($items)) {
            $items = $cartService->getItemsForOrderPayload();
        }

        if (empty($items)) {
            $message = __('Список для бронирования пуст');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return back()->withErrors(['cart' => $message]);
        }

        $request->merge(['items' => $items]);
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $order = $service->create([
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'promo_code' => $data['promo_code'] ?? null,
            'items' => $items,
        ]);

        $cartService->clear();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
            ]);
        }

        return redirect()
            ->route('checkout.index')
            ->with('success_order_id', $order->id);
    }
}
