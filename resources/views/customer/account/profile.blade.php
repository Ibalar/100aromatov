@extends('layouts.app')

@section('title', __('Профиль') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('Профиль')"
        :items="[
            ['title' => __('Личный кабинет'), 'url' => route('customer.account.dashboard')],
            ['title' => __('Профиль')]
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
                        <h3 class="mb-3">{{ __('Данные профиля') }}</h3>

                        @if(session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('customer.account.profile.update') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <fieldset class="tf-field">
                                        <label class="tf-lable fw-medium">{{ __('Имя') }}</label>
                                        <input type="text" name="first_name" value="{{ old('first_name', $customer->first_name) }}">
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="tf-field">
                                        <label class="tf-lable fw-medium">{{ __('Фамилия') }}</label>
                                        <input type="text" name="last_name" value="{{ old('last_name', $customer->last_name) }}">
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="tf-field">
                                        <label class="tf-lable fw-medium">{{ __('Телефон') }}</label>
                                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" inputmode="tel" autocomplete="tel" data-phone-by>
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="tf-field">
                                        <label class="tf-lable fw-medium">{{ __('Email') }}</label>
                                        <input type="email" name="email" value="{{ old('email', $customer->email) }}" required>
                                    </fieldset>
                                </div>
                            </div>
                            <button class="tf-btn animate-btn mt-3" type="submit">{{ __('Сохранить') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
