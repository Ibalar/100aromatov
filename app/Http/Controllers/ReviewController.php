<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::query()
            ->where('is_approved', true)
            ->with([
                'product:id,slug,name_ru,name_by,name',
                'user:id,name',
                'customer:id,first_name,last_name,email',
            ])
            ->latest()
            ->paginate(12);

        $customerStoreReview = null;

        if (auth('customer')->check()) {
            $customerStoreReview = Review::query()
                ->whereNull('product_id')
                ->where('customer_id', auth('customer')->id())
                ->latest()
                ->first();
        }

        return view('reviews.index', compact('reviews', 'customerStoreReview'));
    }

    public function store(Request $request, string $slug, TelegramService $telegramService): RedirectResponse
    {
        $product = Product::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'text' => ['required', 'string', 'min:10', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
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
            'admin_reply' => null,
            'is_approved' => false,
        ]);

        if ($request->hasFile('image')) {
            if (filled($review->image)) {
                Storage::disk('public')->delete($review->image);
            }

            $review->image = $request->file('image')->store('reviews', 'public');
        }

        $review->save();

        $telegramService->send($this->buildTelegramMessage(
            $product,
            $review,
            $customer->full_name,
            route('product.show', $product->slug) . '#customer-reviews',
        ));

        return redirect()
            ->to(route('product.show', $product->slug) . '#customer-reviews')
            ->with('review_success', __('Спасибо. Ваш отзыв отправлен на модерацию.'));
    }

    public function storeStore(Request $request, TelegramService $telegramService): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'text' => ['required', 'string', 'min:10', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $customer = $request->user('customer');

        $review = Review::firstOrNew([
            'product_id' => null,
            'customer_id' => $customer->id,
        ]);

        $review->fill([
            'user_id' => null,
            'rating' => (int) $validated['rating'],
            'text' => trim($validated['text']),
            'admin_reply' => null,
            'is_approved' => false,
        ]);

        if ($request->hasFile('image')) {
            if (filled($review->image)) {
                Storage::disk('public')->delete($review->image);
            }

            $review->image = $request->file('image')->store('reviews', 'public');
        }

        $review->save();

        $telegramService->send($this->buildTelegramMessage(
            null,
            $review,
            $customer->full_name,
            route('reviews.index'),
        ));

        return redirect()
            ->to(route('reviews.index') . '#write-store-review')
            ->with('review_success', __('Спасибо. Ваш отзыв отправлен на модерацию.'));
    }

    private function buildTelegramMessage(?Product $product, Review $review, string $customerName, string $url): string
    {
        $target = $product
            ? 'Товар: ' . e(localizedField($product, 'name'))
            : 'Тип: Отзыв о магазине';

        $author = e($customerName);
        $text = e(Str::limit($review->text, 700));

        return implode("\n", [
            '<b>Новый отзыв на сайте</b>',
            $target,
            "Автор: {$author}",
            "Оценка: {$review->rating}/5",
            "Текст: {$text}",
            "Ссылка: {$url}",
        ]);
    }
}
