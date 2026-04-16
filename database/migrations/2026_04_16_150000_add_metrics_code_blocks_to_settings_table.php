<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('metrics_head_code')->nullable()->after('requisites');
            $table->text('metrics_body_start_code')->nullable()->after('metrics_head_code');
            $table->text('metrics_body_end_code')->nullable()->after('metrics_body_start_code');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'metrics_head_code',
                'metrics_body_start_code',
                'metrics_body_end_code',
            ]);
        });
    }
};
