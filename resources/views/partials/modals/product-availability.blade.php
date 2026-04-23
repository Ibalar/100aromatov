<div class="modal fade" id="productAvailabilityModal" tabindex="-1" aria-labelledby="productAvailabilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content py-4 px-4">
            <div class="modal-heading d-flex align-items-center justify-content-between">
                <h4 class="title-pop" id="productAvailabilityModalLabel">{{ __('Уточнить наличие') }}</h4>
                <span class="cs-pointer d-flex link" data-bs-dismiss="modal">
                    <i class="icon-X2 fs-24"></i>
                </span>
            </div>
            <div class="modal-main">
                @if(session('availability_inquiry_success'))
                    <div class="alert alert-success mb-16">{{ session('availability_inquiry_success') }}</div>
                @endif

                <div class="availability-product-box mb-16">
                    <div class="text-caption-01 cl-text-2">{{ __('Товар') }}</div>
                    <div class="fw-medium" data-availability-product-name>{{ old('product_name') }}</div>
                    <div class="text-caption-01 cl-text-2 mt-4" data-availability-variant-wrap style="{{ old('variant_label') ? '' : 'display:none;' }}">
                        {{ __('Вариант') }}:
                        <span class="fw-medium" data-availability-variant-label>{{ old('variant_label') }}</span>
                    </div>
                </div>

                <form action="{{ route('product.availability-inquiry.store') }}" method="POST" class="tf-grid-layout gap-16">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ old('product_id') }}" data-availability-product-id>
                    <input type="hidden" name="variant_id" value="{{ old('variant_id') }}" data-availability-variant-id>
                    <input type="hidden" value="{{ old('product_name') }}" data-availability-product-name-input>
                    <input type="hidden" value="{{ old('variant_label') }}" data-availability-variant-label-input>

                    <fieldset class="tf-field">
                        <label class="tf-lable fw-medium">{{ __('Ваше имя') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}" required>
                        @error('name', 'availabilityInquiry')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </fieldset>

                    <fieldset class="tf-field">
                        <label class="tf-lable fw-medium">{{ __('Телефон') }}</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required>
                        @error('phone', 'availabilityInquiry')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </fieldset>

                    <fieldset class="tf-field d-flex flex-column">
                        <label class="tf-lable fw-medium">{{ __('Комментарий') }}</label>
                        <textarea name="comment" rows="4" placeholder="{{ __('Например, удобное время для звонка') }}">{{ old('comment') }}</textarea>
                        @error('comment', 'availabilityInquiry')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </fieldset>

                    <button type="submit" class="tf-btn animate-btn w-100">
                        {{ __('Отправить') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .availability-product-box {
            padding: 14px 16px;
            border-radius: 12px;
            background: #f7f7f7;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('productAvailabilityModal');

            if (!modalElement) {
                return;
            }

            const productIdInput = modalElement.querySelector('[data-availability-product-id]');
            const variantIdInput = modalElement.querySelector('[data-availability-variant-id]');
            const productNameInput = modalElement.querySelector('[data-availability-product-name-input]');
            const variantLabelInput = modalElement.querySelector('[data-availability-variant-label-input]');
            const productNameText = modalElement.querySelector('[data-availability-product-name]');
            const variantLabelText = modalElement.querySelector('[data-availability-variant-label]');
            const variantWrap = modalElement.querySelector('[data-availability-variant-wrap]');

            function fillModal(trigger) {
                const productId = trigger.dataset.productId || '';
                const variantId = trigger.dataset.variantId || '';
                const productName = trigger.dataset.productName || '';
                const variantLabel = trigger.dataset.variantLabel || '';

                if (productIdInput) productIdInput.value = productId;
                if (variantIdInput) variantIdInput.value = variantId;
                if (productNameInput) productNameInput.value = productName;
                if (variantLabelInput) variantLabelInput.value = variantLabel;
                if (productNameText) productNameText.textContent = productName;
                if (variantLabelText) variantLabelText.textContent = variantLabel;
                if (variantWrap) variantWrap.style.display = variantLabel ? '' : 'none';
            }

            document.addEventListener('click', function (event) {
                const trigger = event.target.closest('.js-open-availability-modal');

                if (!trigger) {
                    return;
                }

                fillModal(trigger);
            });

            const hasInquiryErrors = @json($errors->availabilityInquiry->isNotEmpty());
            const hasInquirySuccess = @json(session()->has('availability_inquiry_success'));

            if ((hasInquiryErrors || hasInquirySuccess) && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            }
        });
    </script>
@endpush
