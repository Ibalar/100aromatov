<!-- Forgot Pass -->
<div class="modal modalCentered fade modal-log modal-log_forgot" id="modalForgot">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
                <span class="icon-close-popup" data-bs-dismiss="modal">
                    <i class="icon-X2"></i>
                </span>
            <div class="modal-heading text-center">
                <h3 class="title-pop mb-8">{{ __('Забыли пароль') }}</h3>
                <p class="desc-pop cl-text-2">{{ __('Мы отправим инструкции для сброса пароля.') }}</p>
            </div>
            <div class="modal-main">
                <form class="form-log">
                    <div class="form-content">
                        <fieldset class="tf-field">
                            <label for="forgot-user" class="tf-lable fw-medium">
                                {{ __('Имя пользователя или email') }}
                                <span class="text-primary">*</span>
                            </label>
                            <input type="text" id="forgot-user" placeholder="{{ __('Имя пользователя или email') }}*" required>
                        </fieldset>
                    </div>
                    <div class="group-action">
                        <button type="submit" class="tf-btn animate-btn w-100">
                            {{ __('Получить код сброса') }}
                        </button>
                        <p class="orther-log text-center">
                            {{ __('Вспомнили пароль?') }}
                            <a href="#sign" data-bs-toggle="modal" class="text-primary text-decoration-underline">
                                {{ __('Войти') }}
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Forgot Pass -->
