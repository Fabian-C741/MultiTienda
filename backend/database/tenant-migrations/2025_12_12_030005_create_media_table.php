<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('url')->nullable();
            $table->string('filename');
            $table->string('original_name')->nullable();
            $table->string('mime_type');
            $table->unsignedBigInteger('size')->default(0);
            $table->string('type')->default('image'); // image, document
            $table->string('collection')->default('general'); // products, categories, general
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->nullableMorphs('mediable'); // Polymorphic relation opcional
            $table->json('metadata')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['collection']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};