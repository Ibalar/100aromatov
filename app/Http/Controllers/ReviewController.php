<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function store(Request $request, string $slug, TelegramService $telegramService): RedirectResponse
    {
        $product = Product::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'text' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $customer = $request->user('customer');

        $review = Review::firstOrNew([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
        ]);

        $review->fill([
            'user_id' => null,
            'rating' => (int) $validated['rating'],
            'text' => trim($validated['text']),
            'is_approved' => false,
        ]);
        $review->save();

        $telegramService->send($this->buildTelegramMessage($product, $review, $customer->full_name));

        return redirect()
            ->to(route('product.show', $product->slug) . '#customer-reviews')
            ->with('review_success', __('Спасибо. Ваш отзыв отправлен на модерацию.'));
    }

    private function buildTelegramMessage(Product $product, Review $review, string $customerName): string
    {
        $productName = e(localizedField($product, 'name'));
        $author = e($customerName);
        $text = e(Str::limit($review->text, 700));
        $url = route('product.show', $product->slug) . '#customer-reviews';

        return implode("\n", [
            '<b>Новый отзыв на сайте</b>',
            "Товар: {$productName}",
            "Автор: {$author}",
            "Оценка: {$review->rating}/5",
            "Текст: {$text}",
            "Ссылка: {$url}",
        ]);
    }
}
