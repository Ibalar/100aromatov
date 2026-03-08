@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => null,
])

@php
    $title = $title ?? trim($__env->yieldContent('title', config('app.name')));
    $description = $description ?? trim($__env->yieldContent('meta_description', 'Интернет-магазин'));
@endphp

{{-- Open Graph --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image ?? asset('assets/images/logo/logo.png') }}">
<meta property="og:url" content="{{ request()->url() }}">
<meta property="og:type" content="{{ $type ?? 'website' }}">
<meta property="og:locale" content="{{ app()->getLocale() == 'by' ? 'be_BY' : 'ru_RU' }}">
<meta property="og:site_name" content="{{ config('app.name') }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
