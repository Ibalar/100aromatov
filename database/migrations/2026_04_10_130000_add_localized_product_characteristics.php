<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('country_by')->nullable()->after('country');
            $table->string('gender_by')->nullable()->after('gender');
            $table->string('concentration_by')->nullable()->after('concentration');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'country_by',
                'gender_by',
                'concentration_by',
            ]);
        });
    }
};
