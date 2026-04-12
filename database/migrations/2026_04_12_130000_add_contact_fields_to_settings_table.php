<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->json('phones')->nullable()->after('telegram_chat_id');
            $table->text('address')->nullable()->after('phones');
            $table->string('address_map_url')->nullable()->after('address');
            $table->string('instagram_url')->nullable()->after('address_map_url');
            $table->text('requisites')->nullable()->after('instagram_url');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'phones',
                'address',
                'address_map_url',
                'instagram_url',
                'requisites',
            ]);
        });
    }
};
