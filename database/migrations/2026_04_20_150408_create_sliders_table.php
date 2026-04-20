<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();

            // Изображение
            $table->string('background_image');

            // Заголовки
            $table->string('title_ru')->nullable();
            $table->string('title_be')->nullable();

            // Подзаголовки
            $table->string('subtitle_ru')->nullable();
            $table->string('subtitle_be')->nullable();

            // Цвет текста
            $table->string('text_color')->default('#ffffff');

            // Ссылка на кнопку
            $table->string('button_link')->nullable();

            // Сортировка
            $table->integer('sort_order')->default(0);

            // Активность
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
