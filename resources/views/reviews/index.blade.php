@extends('layouts.app')

@section('title', __('reviews.title') . ' - ' . config('app.name'))
@section('meta_description', __('reviews.meta_description'))

@section('content')
    <x-breadcrumbs
        :title="__('reviews.title')"
        :items="[
            ['title' => __('reviews.home'), 'url' => route('home')],
            ['title' => __('reviews.title')],
        ]"
    />

    <section class="flat-spacing-3 pt-0">
        <div class="container">
            <div class="reviews-layout">
                <div class="reviews-list">

                    @if(session('review_success'))
                        <div class="alert alert-success mb-20">{{ session('review_success') }}</div>
                    @endif

                    @forelse($reviews as $review)
                        <article class="review-card">
                            <div class="review-card__head">
                                <div class="review-author">
                                    <div class="review-avatar">
                                        {{ mb_strtoupper(mb_substr($review->author_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="h6 mb-0">{{ $review->author_name }}</p>
                                        <p class="cl-text-3 text-caption-01 mb-0">{{ $review->created_at->format('d.m.Y') }}</p>
                                    </div>
                                </div>
                                <div class="review-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="icon icon-Star {{ $i <= (int) $review->rating ? 'is-active' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>

                            <p class="review-target mb-8">
                                @if($review->product)
                                    {{ __('reviews.product') }}:
                                    <a href="{{ route('product.show', $review->product->slug) }}" class="link">{{ localizedField($review->product, 'name') }}</a>
                                @else
                                    {{ __('reviews.store_review') }}
                                @endif
                            </p>

                            <p class="mb-0">{{ $review->text }}</p>

                            @if(filled($review->image))
                                <div class="review-image-wrap">
                                    <a href="{{ asset('storage/' . $review->image) }}" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('storage/' . $review->image) }}" alt="{{ __('reviews.review_photo') }}" class="review-image">
                                    </a>
                                </div>
                            @endif

                            @if(filled($review->admin_reply))
                                <div class="review-admin-reply">
                                    <p class="review-admin-reply__title">{{ __('reviews.admin_reply') }}</p>
                                    <p class="mb-0">{{ $review->admin_reply }}</p>
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="review-empty">
                            {{ __('reviews.no_reviews') }}
                        </div>
                    @endforelse

                    @if($reviews->hasPages())
                        <div class="mt-20">
                            {{ $reviews->links() }}
                        </div>
                    @endif
                </div>

                <aside class="reviews-form-wrap" id="write-store-review">
                    <h5>{{ __('reviews.leave_store_review') }}</h5>
                    <p class="cl-text-2 mb-16">{{ __('reviews.moderation_note') }}</p>

                    @auth('customer')
                        @if($customerStoreReview)
                            <div class="alert alert-info mb-16">
                                @if($customerStoreReview->is_approved)
                                    {{ __('reviews.already_reviewed') }}
                                @else
                                    {{ __('reviews.previous_on_moderation') }}
                                @endif
                            </div>
                        @endif

                        <form method="POST" action="{{ route('reviews.store') }}" class="form-rating" enctype="multipart/form-data">
                            @csrf
                            <div class="review-rating-select mb-20">
                                <span class="tf-lable fw-medium">{{ __('reviews.your_rating') }}</span>
                                <div class="review-rating-stars">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input
                                            type="radio"
                                            id="store-review-rating-{{ $i }}"
                                            name="rating"
                                            value="{{ $i }}"
                                            {{ (int) old('rating', $customerStoreReview?->rating) === $i ? 'checked' : '' }}
                                        >
                                        <label for="store-review-rating-{{ $i }}" title="{{ $i }}">★</label>
                                    @endfor
                                </div>
                                @error('rating')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <fieldset class="tf-field mb-16">
                                <label class="tf-lable fw-medium">{{ __('reviews.your_name') }}</label>
                                <input type="text" value="{{ auth('customer')->user()->full_name }}" readonly>
                            </fieldset>

                            <fieldset class="tf-field mb-16">
                                <label class="tf-lable fw-medium">{{ __('reviews.your_email') }}</label>
                                <input type="email" value="{{ auth('customer')->user()->email }}" readonly>
                            </fieldset>

                            <fieldset class="tf-field d-flex flex-column mb-20">
                                <label for="store-review-text" class="tf-lable fw-medium">{{ __('reviews.review') }}</label>
                                <textarea name="text" id="store-review-text" placeholder="{{ __('reviews.share_store_impression') }}">{{ old('text', $customerStoreReview?->text) }}</textarea>
                                @error('text')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </fieldset>

                            <fieldset class="tf-field mb-20">
                                <label for="store-review-image" class="tf-lable fw-medium">{{ __('reviews.review_photo_optional') }}</label>
                                <input type="file" name="image" id="store-review-image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                <p class="cl-text-3 text-caption-01 mt-8 mb-0">{{ __('reviews.review_photo_formats') }}</p>
                                @error('image')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </fieldset>

                            <button type="submit" class="tf-btn animate-btn w-100">
                                {{ __('reviews.submit_review') }}
                            </button>
                        </form>
                    @else
                        <div class="review-login-note">
                            <p class="cl-text-2 mb-12">{{ __('reviews.auth_required') }}</p>
                            <div class="d-flex flex-wrap gap-12">
                                <a href="{{ route('customer.login') }}" class="tf-btn animate-btn">{{ __('reviews.login') }}</a>
                                <a href="{{ route('customer.register') }}" class="tf-btn btn-line">{{ __('reviews.register') }}</a>
                            </div>
                        </div>
                    @endauth

                    <div class="review-rules mb-20">
                        <p class="mb-8">{{ __('reviews.rules_intro_short') }}</p>
                        <details class="review-rules__more">
                            <summary>{{ __('reviews.rules_read_full') }}</summary>
                            <ul class="review-rules__list mb-10 mt-10">
                                <li>{{ __('reviews.rules_item_1') }}</li>
                                <li>{{ __('reviews.rules_item_2') }}</li>
                                <li>{{ __('reviews.rules_item_3') }}</li>
                                <li>{{ __('reviews.rules_item_4') }}</li>
                                <li>{{ __('reviews.rules_item_5') }}</li>
                                <li>{{ __('reviews.rules_item_6') }}</li>
                                <li>{{ __('reviews.rules_item_7') }}</li>
                                <li>{{ __('reviews.rules_item_8') }}</li>
                                <li>{{ __('reviews.rules_item_9') }}</li>
                                <li>{{ __('reviews.rules_item_10') }}</li>
                                <li>{{ __('reviews.rules_item_11') }}</li>
                                <li>{{ __('reviews.rules_item_12') }}</li>
                                <li>{{ __('reviews.rules_item_13') }}</li>
                            </ul>
                            <p class="mb-0">{{ __('reviews.rules_outro') }}</p>
                        </details>
                    </div>

                    @php
                        $googleReviewsUrl = trim((string) ($siteSettings->google_reviews_url ?? ''));
                        $yandexReviewsUrl = trim((string) ($siteSettings->yandex_reviews_url ?? ''));
                    @endphp

                    @if($googleReviewsUrl !== '' || $yandexReviewsUrl !== '')
                        <div class="external-reviews-links">
                            <p class="external-reviews-links__title mb-10">{{ __('reviews.leave_review_on_maps') }}</p>
                            <div class="d-flex flex-column gap-10">
                                @if($googleReviewsUrl !== '')
                                    <a href="{{ $googleReviewsUrl }}" target="_blank" rel="noopener noreferrer" class="tf-btn btn-line w-100">
                                        {{ __('reviews.leave_review_google') }}
                                    </a>
                                @endif
                                @if($yandexReviewsUrl !== '')
                                    <a href="{{ $yandexReviewsUrl }}" target="_blank" rel="noopener noreferrer" class="tf-btn btn-line w-100">
                                        {{ __('reviews.leave_review_yandex') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif




                </aside>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .reviews-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 380px;
        gap: 24px;
        align-items: start;
    }

    .review-card {
        border: 1px solid #eee;
        border-radius: 16px;
        padding: 20px;
    }

    .review-card + .review-card {
        margin-top: 16px;
    }

    .review-card__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .review-author {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .review-stars {
        display: inline-flex;
        gap: 2px;
    }

    .review-stars .icon-Star {
        color: #d0d0d0;
    }

    .review-stars .icon-Star.is-active {
        color: #f5b301;
    }

    .review-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: #181818;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .reviews-form-wrap {
        border: 1px solid #eee;
        border-radius: 16px;
        padding: 20px;
        position: sticky;
        top: 100px;
    }

    .review-empty {
        border: 1px dashed #d9d9d9;
        border-radius: 12px;
        padding: 20px;
        background: #fafafa;
    }

    .review-rating-select {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .review-rating-stars {
        display: inline-flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 4px;
    }

    .review-rating-stars input {
        display: none;
    }

    .review-rating-stars label {
        cursor: pointer;
        font-size: 28px;
        line-height: 1;
        color: #d0d0d0;
    }

    .review-rating-stars label:hover,
    .review-rating-stars label:hover ~ label,
    .review-rating-stars input:checked ~ label {
        color: #f5b301;
    }

    .review-login-note {
        border: 1px dashed #d9d9d9;
        border-radius: 16px;
        padding: 20px;
        background: #fafafa;
    }

    .review-image-wrap {
        margin-top: 12px;
    }

    .review-image {
        display: block;
        max-width: 220px;
        width: 100%;
        border-radius: 10px;
        border: 1px solid #eee;
    }

    .review-admin-reply {
        margin-top: 14px;
        padding: 14px 16px;
        border-left: 3px solid #181818;
        background: #f8f8f8;
        border-radius: 8px;
    }

    .review-admin-reply__title {
        margin-bottom: 6px;
        font-weight: 600;
    }

    .external-reviews-links {
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #eee;
    }

    .review-rules {
        padding: 14px 16px;
        border: 1px solid #eee;
        border-radius: 12px;
        background: #fafafa;
        font-size: 14px;
        line-height: 1.45;
    }

    .review-rules__list {
        padding-left: 18px;
        margin: 0;
    }

    .review-rules__list li + li {
        margin-top: 6px;
    }

    .review-rules__more summary {
        cursor: pointer;
        font-weight: 600;
        color: #181818;
        user-select: none;
    }

    .external-reviews-links__title {
        font-weight: 600;
    }

    @media (max-width: 991px) {
        .reviews-layout {
            grid-template-columns: 1fr;
        }

        .reviews-form-wrap {
            position: static;
        }
    }
</style>
@endpush
