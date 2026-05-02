<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('google_reviews_url')->nullable()->after('instagram_url');
            $table->string('yandex_reviews_url')->nullable()->after('google_reviews_url');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['google_reviews_url', 'yandex_reviews_url']);
        });
    }
};
