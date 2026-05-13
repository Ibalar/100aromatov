<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promo_code_usages', function (Blueprint $table) {
            $table->string('phone', 32)->nullable()->after('customer_id');
            $table->index(['promo_code_id', 'phone'], 'pcu_promo_phone_idx');
        });
    }

    public function down(): void
    {
        Schema::table('promo_code_usages', function (Blueprint $table) {
            $table->dropIndex('pcu_promo_phone_idx');
            $table->dropColumn('phone');
        });
    }
};
