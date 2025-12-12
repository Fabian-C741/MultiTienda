<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gateway'); // mercadopago, uala, transfer, cash
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_sandbox')->default(true);
            $table->json('credentials')->nullable(); // encrypted credentials
            $table->json('settings')->nullable(); // gateway-specific settings
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['gateway']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
