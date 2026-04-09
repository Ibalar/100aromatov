<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable()->index();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('provider')->nullable()->index();
            $table->string('provider_id')->nullable()->index();
            $table->string('avatar')->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->json('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_enabled_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->unique(['provider', 'provider_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

