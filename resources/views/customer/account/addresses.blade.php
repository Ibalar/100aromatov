@extends('layouts.app')

@section('title', __('Адреса') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('Адреса')"
        :items="[
            ['title' => __('Личный кабинет'), 'url' => route('customer.account.dashboard')],
            ['title' => __('Адреса')]
        ]"
    />

    <section class="flat-spacing-3 pt-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    @include('customer.account._nav')
                </div>
                <div class="col-lg-9">
                    <div class="card p-4">
                        <h3 class="mb-3">{{ __('Адресная книга') }}</h3>
                        <p class="cl-text-2 mb-0">{{ __('Раздел подготовлен для расширения. Здесь можно будет хранить адреса доставки и платежные реквизиты.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

