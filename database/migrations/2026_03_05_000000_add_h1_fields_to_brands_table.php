<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table): void {
            $table->string('h1_title_ru')->nullable()->after('seo_description_by');
            $table->string('h1_title_by')->nullable()->after('h1_title_ru');
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table): void {
            $table->dropColumn(['h1_title_ru', 'h1_title_by']);
        });
    }
};
