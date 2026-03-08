<?php

if (! function_exists('localizedField')) {
    function localizedField($model, string $field): mixed
    {
        if (! $model) {
            return null;
        }

        $locale = app()->getLocale();
        $localizedField = sprintf('%s_%s', $field, $locale);
        $value = $model->getAttribute($localizedField);

        if (! blank($value)) {
            return $value;
        }

        $fallbackLocale = $locale === 'by' ? 'ru' : 'by';
        $fallbackField = sprintf('%s_%s', $field, $fallbackLocale);
        $fallbackValue = $model->getAttribute($fallbackField);

        if (! blank($fallbackValue)) {
            return $fallbackValue;
        }

        return $model->getAttribute($field);
    }
}
