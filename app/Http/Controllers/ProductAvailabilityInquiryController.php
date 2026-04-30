<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductAvailabilityInquiryController extends Controller
{
    public function store(Request $request, TelegramService $telegramService): RedirectResponse
    {
        $validated = $request->validateWithBag('availabilityInquiry', [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'privacy_policy' => ['accepted'],
            'website' => ['nullable', 'size:0'],
            'form_started_at' => ['required', 'integer'],
        ]);

        if (now()->timestamp - (int) $validated['form_started_at'] < 2) {
            return back()
                ->withErrors([
                    'phone' => __('Форма отправлена слишком быстро. Попробуйте еще раз.'),
                ], 'availabilityInquiry')
                ->withInput();
        }

        if (! isValidBelarusMobilePhone($validated['phone'])) {
            return back()
                ->withErrors([
                    'phone' => __('Введите корректный номер телефона белорусского оператора.'),
                ], 'availabilityInquiry')
                ->withInput();
        }

        $validated['phone'] = formatBelarusMobilePhone($validated['phone']) ?? $validated['phone'];

        $product = Product::query()->findOrFail($validated['product_id']);
        $variant = ! empty($validated['variant_id'])
            ? ProductVariant::query()
                ->where('product_id', $product->id)
                ->find($validated['variant_id'])
            : null;

        $telegramService->send($this->buildTelegramMessage($product, $variant, $validated));

        return back()->with('availability_inquiry_success', __('Спасибо! Мы уточним наличие и свяжемся с вами.'));
    }

    private function buildTelegramMessage(Product $product, ?ProductVariant $variant, array $data): string
    {
        $productName = htmlspecialchars((string) localizedField($product, 'name'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $name = htmlspecialchars((string) $data['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $phone = htmlspecialchars((string) $data['phone'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $comment = htmlspecialchars((string) ($data['comment'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $message = "<b>Запрос наличия товара</b>\n";
        $message .= "Товар: {$productName}\n";

        if ($variant) {
            $variantLabel = trim(($variant->volume_ml ? $variant->volume_ml . ' ml' : '') ?: ($variant->sku ?: ''));
            if ($variantLabel !== '') {
                $message .= 'Вариант: ' . htmlspecialchars($variantLabel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "\n";
            }

            if ($variant->sku) {
                $message .= 'SKU: ' . htmlspecialchars($variant->sku, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "\n";
            }
        }

        $message .= "Имя: {$name}\n";
        $message .= "Телефон: {$phone}\n";
        $message .= 'Ссылка: ' . route('product.show', $product->slug) . "\n";

        if ($comment !== '') {
            $message .= "\nКомментарий:\n{$comment}";
        }

        return $message;
    }
}
