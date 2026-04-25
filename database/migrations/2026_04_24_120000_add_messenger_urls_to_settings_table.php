<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('telegram_url')->nullable()->after('telegram_chat_id');
            $table->string('viber_url')->nullable()->after('telegram_url');
            $table->string('whatsapp_url')->nullable()->after('viber_url');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'telegram_url',
                'viber_url',
                'whatsapp_url',
            ]);
        });
    }
};
