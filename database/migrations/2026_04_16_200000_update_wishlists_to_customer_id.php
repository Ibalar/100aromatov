<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wishlists', function (Blueprint $table): void {
            if (Schema::hasColumn('wishlists', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropUnique(['user_id', 'product_id']);
                $table->dropColumn('user_id');
            }

            if (! Schema::hasColumn('wishlists', 'customer_id')) {
                $table->foreignId('customer_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('customers')
                    ->cascadeOnDelete();

                $table->unique(['customer_id', 'product_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table): void {
            if (Schema::hasColumn('wishlists', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropUnique(['customer_id', 'product_id']);
                $table->dropColumn('customer_id');
            }

            if (! Schema::hasColumn('wishlists', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->unique(['user_id', 'product_id']);
            }
        });
    }
};
