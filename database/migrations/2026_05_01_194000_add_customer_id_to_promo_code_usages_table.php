<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promo_code_usages', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->nullOnDelete();
        });

        DB::table('promo_code_usages')
            ->whereNull('customer_id')
            ->update([
                'customer_id' => DB::raw('(SELECT customer_id FROM orders WHERE orders.id = promo_code_usages.order_id)'),
            ]);

        Schema::table('promo_code_usages', function (Blueprint $table) {
            $table->index(['promo_code_id', 'customer_id'], 'pcu_promo_customer_idx');
        });
    }

    public function down(): void
    {
        Schema::table('promo_code_usages', function (Blueprint $table) {
            $table->dropIndex('pcu_promo_customer_idx');
            $table->dropConstrainedForeignId('customer_id');
        });
    }
};
