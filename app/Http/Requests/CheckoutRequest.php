<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string'],
            'call_preference' => ['required', Rule::in(['call_me', 'no_call'])],
            'email' => ['nullable', 'email'],
            'promo_code' => ['nullable', 'string', 'max:64'],
            'privacy_policy' => ['accepted'],
            'website' => ['nullable', 'size:0'],
            'form_started_at' => ['required', 'integer', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.variant_id' => ['required', 'exists:product_variants,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => __('Введите номер телефона.'),
            'call_preference.required' => __('Укажите предпочтения по звонку.'),
            'call_preference.in' => __('Недопустимое значение предпочтения звонка.'),
            'email.email' => __('Введите корректный email.'),
            'privacy_policy.accepted' => __('Необходимо согласие на обработку персональных данных.'),
            'form_started_at.required' => __('Ошибка проверки формы.'),
            'items.required' => __('Список для бронирования пуст.'),
            'items.*.variant_id.required' => __('Товар не указан.'),
            'items.*.variant_id.exists' => __('Выбранный товар не найден.'),
            'items.*.qty.required' => __('Укажите количество.'),
            'items.*.qty.min' => __('Количество должно быть не менее 1.'),
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'email' => filled(trim((string) $this->input('email')))
                ? trim((string) $this->input('email'))
                : null,
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $this->validated();

            if (now()->timestamp - (int) ($data['form_started_at'] ?? 0) < 2) {
                $validator->errors()->add(
                    'phone',
                    __('Форма отправлена слишком быстро. Попробуйте еще раз.')
                );
            }

            if (filled($data['phone'] ?? null) && ! isValidBelarusMobilePhone((string) $data['phone'])) {
                $validator->errors()->add(
                    'phone',
                    __('Введите корректный номер телефона белорусского оператора.')
                );
            }
        });
    }

    protected function passedValidation(): void
    {
        Log::debug('CheckoutRequest: validation passed', [
            'phone' => $this->maskPhone((string) $this->validated('phone')),
            'call_preference' => $this->validated('call_preference'),
            'has_email' => filled($this->validated('email')),
            'has_promo' => filled($this->validated('promo_code')),
            'items_count' => count($this->validated('items')),
        ]);
    }

    private function maskPhone(string $phone): string
    {
        $normalized = normalizeBelarusPhone($phone);

        if (! $normalized || strlen($normalized) < 7) {
            return '***';
        }

        return substr($normalized, 0, 5).'***'.substr($normalized, -2);
    }
}
