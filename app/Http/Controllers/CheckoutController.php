<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\CartService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
        $request->merge([
            'email' => filled(trim((string) $request->input('email')))
                ? trim((string) $request->input('email'))
                : null,
        ]);

        $data = $request->validate([
            'phone' => 'required|string',
            'call_preference' => 'required|in:call_me,no_call',
            'email' => 'nullable|email',
            'promo_code' => 'nullable|string|max:64',
            'privacy_policy' => 'accepted',
            'website' => 'nullable|size:0',
            'form_started_at' => 'required|integer',
        ]);

        if (now()->timestamp - (int) $data['form_started_at'] < 2) {
            return back()->withErrors([
                'phone' => __('Форма отправлена слишком быстро. Попробуйте еще раз.'),
            ])->withInput();
        }

        if (! isValidBelarusMobilePhone($data['phone'])) {
            return back()->withErrors([
                'phone' => __('Введите корректный номер телефона белорусского оператора.'),
            ])->withInput();
        }

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
            'phone' => formatBelarusMobilePhone($data['phone']) ?? $data['phone'],
            'call_preference' => $data['call_preference'],
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
