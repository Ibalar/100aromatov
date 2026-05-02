@extends('layouts.app')

@section('title', localizedField($page, 'seo_title') ?: localizedField($page, 'name') . ' - ' . config('app.name'))
@section('meta_description', localizedField($page, 'seo_description') ?: localizedField($page, 'name'))

@push('styles')
    <x-seo-meta
        :title="localizedField($page, 'seo_title') ?: localizedField($page, 'name')"
        :description="localizedField($page, 'seo_description') ?: localizedField($page, 'name')"
        :type="'article'"
    />
@endpush

@section('content')
    @php
        $pageName = localizedField($page, 'name');
        $pageDescription = localizedField($page, 'description');
        $slug = mb_strtolower((string) $page->slug);

        $faqItems = [];

        if (
            str_contains($slug, 'dostav')
            || str_contains($slug, 'delivery')
            || str_contains($slug, 'oplata')
            || str_contains($slug, 'payment')
            || str_contains($slug, 'zakaz')
            || str_contains($slug, 'order')
        ) {
            $faqItems = [
                [
                    'q' => __('Как оформить заказ?'),
                    'a' => __('Выберите товар, добавьте его в корзину и подтвердите заказ через форму оформления.'),
                ],
                [
                    'q' => __('Как оплатить заказ?'),
                    'a' => __('Доступные варианты оплаты указаны на этой странице.'),
                ],
                [
                    'q' => __('Какие сроки доставки?'),
                    'a' => __('Сроки и условия доставки зависят от вашего города и подтверждаются при обработке заказа.'),
                ],
            ];
        }
    @endphp

    @if($faqItems !== [])
        <x-faq-schema :items="$faqItems" />
    @endif

    <x-breadcrumbs
        :title="$pageName"
        :items="[
            ['title' => $pageName]
        ]"
    />

    <section class="flat-spacing-3 pt-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="static-page-content">

                        @if($pageDescription)
                            <div class="cl-text text-body-1">
                                {!! $pageDescription !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
