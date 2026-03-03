<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();

            $table->string('type'); // percent / fixed
            $table->decimal('value', 10, 2);

            $table->decimal('min_order_usd', 10, 2)->nullable();

            $table->integer('usage_limit')->nullable();
            $table->integer('usage_per_user')->nullable();

            $table->dateTime('active_from')->nullable();
            $table->dateTime('active_to')->nullable();

            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
