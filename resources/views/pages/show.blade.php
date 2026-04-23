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
    @endphp

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
