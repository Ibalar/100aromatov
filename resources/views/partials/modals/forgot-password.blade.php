<!-- Forgot Pass -->
<div class="modal modalCentered fade modal-log modal-log_forgot" id="modalForgot">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
                <span class="icon-close-popup" data-bs-dismiss="modal">
                    <i class="icon-X2"></i>
                </span>
            <div class="modal-heading text-center">
                <h3 class="title-pop mb-8">Forgot Password</h3>
                <p class="desc-pop cl-text-2">We’ll send instructions to reset your password.</p>
            </div>
            <div class="modal-main">
                <form class="form-log">
                    <div class="form-content">
                        <fieldset class="tf-field">
                            <label for="forgot-user" class="tf-lable fw-medium">
                                Username or email address
                                <span class="text-primary">*</span>
                            </label>
                            <input type="text" id="forgot-user" placeholder="Username or email address*" required>
                        </fieldset>
                    </div>
                    <div class="group-action">
                        <button type="submit" class="tf-btn animate-btn w-100">
                            Get Reset Code
                        </button>
                        <p class="orther-log text-center">
                            Remember your password?
                            <a href="#sign" data-bs-toggle="modal" class="text-primary text-decoration-underline">
                                Sign In
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Forgot Pass -->
