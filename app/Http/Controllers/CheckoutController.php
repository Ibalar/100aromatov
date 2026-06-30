<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\CartService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

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

    public function store(CheckoutRequest $request, OrderService $service, CartService $cartService)
    {
        $data = $request->validated();

        $items = $data['items'] ?? [];
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

        try {
            $order = $service->create([
                'phone' => formatBelarusMobilePhone($data['phone']) ?? $data['phone'],
                'call_preference' => $data['call_preference'],
                'email' => $data['email'] ?? null,
                'promo_code' => $data['promo_code'] ?? null,
                'items' => $items,
            ]);
        } catch (\Throwable $e) {
            Log::error('Checkout: order creation failed', [
                'error' => $e->getMessage(),
                'phone' => isset($data['phone']) ? normalizeBelarusPhone($data['phone']) : null,
                'items_count' => count($items),
            ]);

            $message = __('Произошла ошибка при оформлении заказа. Пожалуйста, попробуйте позже.');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500);
            }

            return back()
                ->withErrors(['phone' => $message])
                ->withInput();
        }

        $cartService->clear();

        session(['last_order_id' => $order->id]);

        Log::info('Checkout: order created', [
            'order_id' => $order->id,
            'total_byn' => $order->total_byn,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
            ]);
        }

        return redirect()
            ->route('checkout.success', $order);
    }

    public function success(Order $order)
    {
        $lastOrderId = session('last_order_id');

        if ((int) $lastOrderId !== (int) $order->id) {
            return redirect()->route('home');
        }
        $order->load('items');

        Log::info('Checkout: success page viewed', [
            'order_id' => $order->id,
        ]);

        $settings = Setting::getSettings();

        return view('checkout.success', [
            'order' => $order,
            'settings' => $settings,
        ]);
    }

    public function promoSummary(Request $request, OrderService $service, CartService $cartService): JsonResponse
    {
        $data = $request->validate([
            'promo_code' => 'nullable|string|max:64',
            'phone' => 'nullable|string',
        ]);

        $items = $cartService->getItemsForOrderPayload();
        if (empty($items)) {
            return response()->json([
                'success' => true,
                'items' => [],
                'total_byn' => 0,
                'total_byn_formatted' => number_format(0, 2, ',', ' ') . ' BYN',
                'promo_error' => null,
            ]);
        }

        $phone = null;
        if (filled($data['phone'] ?? null)) {
            $phone = formatBelarusMobilePhone((string) $data['phone']) ?? trim((string) $data['phone']);
        }

        $preview = $service->calculatePreview(
            $items,
            $data['promo_code'] ?? null,
            auth('customer')->id(),
            $phone
        );

        $settings = Setting::getSettings();
        $mappedItems = [];

        foreach ($preview['items'] as $item) {
            $priceByn = $settings->convertUsdToByn($item['price_usd']);
            $lineByn = round($priceByn * $item['qty'], 2);

            $mappedItems[] = [
                'variant_id' => (int) $item['variant']->id,
                'price_byn' => $priceByn,
                'line_byn' => $lineByn,
            ];
        }

        $totalByn = $settings->convertUsdToByn($preview['total_usd']);
        $discountByn = $settings->convertUsdToByn($preview['total_discount_usd']);

        return response()->json([
            'success' => true,
            'items' => $mappedItems,
            'total_byn' => $totalByn,
            'total_byn_formatted' => number_format($totalByn, 2, ',', ' ') . ' BYN',
            'discount_byn' => $discountByn,
            'discount_byn_formatted' => number_format($discountByn, 2, ',', ' ') . ' BYN',
            'promo_error' => $preview['promo_error'],
        ]);
    }
}
