<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('yandex_rating', 3, 2)->nullable()->after('yandex_reviews_url');
            $table->unsignedInteger('yandex_reviews_count')->nullable()->after('yandex_rating');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['yandex_rating', 'yandex_reviews_count']);
        });
    }
};
