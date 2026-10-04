<?php

declare(strict_types=1);

namespace App\MoonShine\Fields;

use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;
use MoonShine\TinyMce\Fields\TinyMce as BaseTinyMce;

/**
 * Self-hosted TinyMCE без загрузки с cdn.tiny.cloud.
 *
 * Проблема вендорного поля: при установленном TINYMCE_TOKEN
 * в assets() подключается https://cdn.tiny.cloud/1/{token}/.../plugins.min.js,
 * у которого считается квота editor loads. Когда квота исчерпана,
 * TinyMCE переходит в read-only: "you have no more editor loads available".
 *
 * Это поле переопределяет проверку ключа: токен всегда пустой,
 * грузятся только локальные ассеты из public/vendor/moonshine-tinymce
 * (публикуются из vendor/moonshine/tinymce/public).
 */
class TinyMce extends BaseTinyMce
{
    protected function assets(): array
    {
        return [
            Js::make('vendor/moonshine-tinymce/tinymce.min.js'),
            Js::make('vendor/moonshine-tinymce/init.js'),
            Css::make('vendor/moonshine-tinymce/tinymce.css'),
        ];
    }

    protected function getToken(): string
    {
        return '';
    }
}
