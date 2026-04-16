<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filter_pages', function (Blueprint $table): void {
            $table->boolean('show_in_category_tiles')
                ->default(true)
                ->after('is_indexable')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('filter_pages', function (Blueprint $table): void {
            $table->dropColumn('show_in_category_tiles');
        });
    }
};
