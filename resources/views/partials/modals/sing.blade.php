<!-- Register -->
<div class="modal modalCentered fade modal-log" id="register">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
                <span class="icon-close-popup" data-bs-dismiss="modal">
                    <i class="icon-X2"></i>
                </span>
            <div class="modal-heading text-center">
                <h3 class="title-pop mb-8">{{ __('Создать аккаунт') }}</h3>
                <p class="desc-pop cl-text-2">{{ __('Станьте частью нашей растущей семьи новых клиентов!') }}</p>
            </div>
            <div class="modal-main">
                <form method="POST" action="{{ route('customer.register.store') }}" class="form-log">
                    @csrf
                    <div class="form-content">
                        <fieldset class="tf-field">
                            <label for="user-name" class="tf-lable fw-medium">{{ __('Имя пользователя или email') }} <span
                                    class="text-primary">*</span></label>
                            <input type="email" id="user-name" name="email" value="{{ old('email') }}" placeholder="{{ __('Имя пользователя или email') }}*" required>
                            @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </fieldset>
                        <fieldset class="tf-field password-wrapper">
                            <label for="register-password" class="tf-lable fw-medium">
                                {{ __('Пароль') }}
                                <span class="text-primary">*</span>
                            </label>
                            <div class="password-wrapper w-100">
                                <span class="toggle-pass icon-EyeSlash fs-20 cl-text-3"></span>
                                <input class="password-field" type="password" id="register-password" name="password"
                                       placeholder="{{ __('Пароль') }}" required>
                            </div>
                            @error('password') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </fieldset>
                        <fieldset class="tf-field password-wrapper">
                            <label for="register-password-confirm" class="tf-lable fw-medium">
                                {{ __('Подтвердите пароль') }}
                                <span class="text-primary">*</span>
                            </label>
                            <div class="password-wrapper w-100">
                                <span class="toggle-pass icon-EyeSlash fs-20 cl-text-3"></span>
                                <input class="password-field" type="password" id="register-password-confirm" name="password_confirmation"
                                       placeholder="{{ __('Подтвердите пароль') }}" required>
                            </div>
                        </fieldset>
                    </div>
                    <div class="group-action">
                        <button type="submit" class="action-create-account tf-btn animate-btn w-100">
                            {{ __('Создать аккаунт') }}
                        </button>
                        <a href="#sign" data-bs-toggle="modal" class="tf-btn btn-stroke">
                            {{ __('Войти') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Register -->
<!-- Sign In -->
<div class="modal modalCentered fade modal-log" id="sign">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
                <span class="icon-close-popup" data-bs-dismiss="modal">
                    <i class="icon-X2"></i>
                </span>
            <div class="modal-heading text-center">
                <h3 class="title-pop mb-8">{{ __('Вход') }}</h3>
                <p class="desc-pop cl-text-2">{{ __('Войдите, чтобы получить доступ к персонализированному опыту.') }}</p>
            </div>
            <div class="modal-main">
                <form method="POST" action="{{ route('customer.login.store') }}" class="form-log">
                    @csrf
                    <div class="form-content">
                        <fieldset class="tf-field">
                            <label for="user-name-log" class="tf-lable fw-medium">{{ __('Имя пользователя или email') }} <span
                                    class="text-primary">*</span></label>
                            <input type="email" id="user-name-log" name="email" value="{{ old('email') }}" placeholder="{{ __('Имя пользователя или email') }}*" required>
                            @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </fieldset>
                        <fieldset class="tf-field password-wrapper">
                            <label for="password" class="tf-lable fw-medium">
                                {{ __('Пароль') }}
                                <span class="text-primary">*</span>
                            </label>
                            <div class="password-wrapper w-100">
                                <span class="toggle-pass icon-EyeSlash fs-20 cl-text-3"></span>
                                <input class="password-field" type="password" id="password" name="password" placeholder="{{ __('Пароль') }}"
                                       required>
                            </div>
                            @error('password') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </fieldset>
                        <fieldset class="field-bottom">
                            <div class="checkbox-wrap">
                                <input class="tf-check style-2" type="checkbox" id="remember" name="remember" value="1">
                                <label for="remember">
                                    {{ __('Запомнить меня') }}
                                </label>
                            </div>
                            <a href="#modalForgot" data-bs-toggle="modal" class="link text-decoration-underline">
                                    <span class="text-caption-01 fw-semibold">
                                        {{ __('Забыли пароль?') }}
                                    </span>
                            </a>
                        </fieldset>
                    </div>
                    <div class="group-action">
                        <button type="submit" class="tf-btn animate-btn w-100">
                            {{ __('Войти') }}
                        </button>
                        <a href="#register" data-bs-toggle="modal" class="tf-btn btn-stroke">
                            {{ __('Создать аккаунт') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Sign In -->
