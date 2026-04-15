@extends('layouts.app')

@section('title', __('Вход') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('Вход')"
        :items="[
            ['title' => __('Вход')]
        ]"
    />

    <section class="flat-spacing-3 pt-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card p-4">
                        <h3 class="mb-3 text-center">{{ __('Вход в личный кабинет') }}</h3>
                        <form method="POST" action="{{ route('customer.login.store') }}">
                            @csrf
                            <fieldset class="tf-field mb-15">
                                <label class="tf-lable fw-medium">{{ __('Email') }}</label>
                                <input type="email" name="email" value="{{ old('email') }}" required>
                                @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </fieldset>

                            <fieldset class="tf-field mb-15">
                                <label class="tf-lable fw-medium">{{ __('Пароль') }}</label>
                                <input type="password" name="password" required>
                                @error('password') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </fieldset>

                            <div class="checkbox-wrap mb-15">
                                <input class="tf-check style-2" type="checkbox" id="remember" name="remember" value="1">
                                <label for="remember">{{ __('Запомнить меня') }}</label>
                            </div>

                            <button type="submit" class="tf-btn animate-btn w-100 mb-10">{{ __('Войти') }}</button>
                        </form>

                        <div class="text-center mt-15">
                            <a href="{{ route('customer.register') }}" class="link text-decoration-underline">{{ __('Нет аккаунта? Зарегистрироваться') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
