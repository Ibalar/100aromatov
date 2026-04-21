<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_hidden_category_is_accessible_by_slug(): void
    {
        $category = Category::create([
            'slug' => 'hidden-cat',
            'name_ru' => 'Скрытая категория',
            'name_by' => 'Схаваная катэгорыя',
            'is_active' => true,
            'show_in_menu' => false,
        ]);

        $response = $this->get(route('category.show', $category->slug));

        $response->assertOk();
        $response->assertSee('Скрытая категория');
    }

    public function test_inactive_category_is_not_accessible(): void
    {
        $category = Category::create([
            'slug' => 'inactive-cat',
            'name_ru' => 'Неактивная категория',
            'name_by' => 'Неактыўная катэгорыя',
            'is_active' => false,
            'show_in_menu' => true,
        ]);

        $response = $this->get(route('category.show', $category->slug));

        $response->assertNotFound();
    }

    public function test_hidden_category_is_not_in_menu(): void
    {
        $category = Category::create([
            'slug' => 'hidden-cat',
            'name_ru' => 'Скрытая категория',
            'name_by' => 'Схаваная катэгорыя',
            'is_active' => true,
            'show_in_menu' => false,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Скрытая категория');
    }

    public function test_hidden_category_is_not_in_category_index(): void
    {
        $category = Category::create([
            'slug' => 'hidden-cat',
            'name_ru' => 'Скрытая категория',
            'name_by' => 'Схаваная катэгорыя',
            'is_active' => true,
            'show_in_menu' => false,
        ]);

        $response = $this->get(route('categories.index'));

        $response->assertOk();
        $response->assertDontSee('Скрытая категория');
    }
}
