@extends('layouts.app')

@section('title', __('Регистрация') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('Регистрация')"
        :items="[
            ['title' => __('Регистрация')]
        ]"
    />

    <section class="flat-spacing-3 pt-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card p-4">
                        <h3 class="mb-3">{{ __('Создать аккаунт') }}</h3>
                        <form method="POST" action="{{ route('customer.register.store') }}">
                            @csrf
                            <fieldset class="tf-field mb-3">
                                <label class="tf-lable fw-medium">E-mail</label>
                                <input type="email" name="email" value="{{ old('email') }}" required>
                                @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </fieldset>

                            <fieldset class="tf-field mb-3">
                                <label class="tf-lable fw-medium">{{ __('Пароль') }}</label>
                                <input type="password" name="password" required>
                                @error('password') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </fieldset>

                            <fieldset class="tf-field mb-3">
                                <label class="tf-lable fw-medium">{{ __('Подтвердите пароль') }}</label>
                                <input type="password" name="password_confirmation" required>
                            </fieldset>

                            <button type="submit" class="tf-btn animate-btn w-100">{{ __('Зарегистрироваться') }}</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('customer.login') }}" class="link text-decoration-underline">{{ __('Уже есть аккаунт? Войти') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

