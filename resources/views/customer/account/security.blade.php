@extends('layouts.app')

@section('title', __('Безопасность') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('Безопасность')"
        :items="[
            ['title' => __('Личный кабинет'), 'url' => route('customer.account.dashboard')],
            ['title' => __('Безопасность')]
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
                        <h3 class="mb-3">{{ __('Смена пароля') }}</h3>

                        @if(session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('customer.account.security.update') }}">
                            @csrf
                            <fieldset class="tf-field mb-3">
                                <label class="tf-lable fw-medium">{{ __('Текущий пароль') }}</label>
                                <input type="password" name="current_password" required>
                                @error('current_password') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </fieldset>
                            <fieldset class="tf-field mb-3">
                                <label class="tf-lable fw-medium">{{ __('Новый пароль') }}</label>
                                <input type="password" name="password" required>
                                @error('password') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </fieldset>
                            <fieldset class="tf-field mb-3">
                                <label class="tf-lable fw-medium">{{ __('Подтверждение нового пароля') }}</label>
                                <input type="password" name="password_confirmation" required>
                            </fieldset>
                            <button class="tf-btn animate-btn" type="submit">{{ __('Обновить пароль') }}</button>
                        </form>

                        <hr class="my-4">
                        <p class="mb-1"><strong>{{ __('Двухфакторная защита') }}</strong></p>
                        <p class="cl-text-2 mb-0">{{ __('Раздел подготовлен для последующего подключения 2FA (TOTP/SMS/e-mail).') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

