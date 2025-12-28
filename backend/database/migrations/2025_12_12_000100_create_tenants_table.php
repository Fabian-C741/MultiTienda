<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subdomain')->unique();
            $table->text('description')->nullable();
            $table->string('domain')->nullable()->unique();
            $table->string('database')->unique();
            $table->string('database_host')->default('127.0.0.1');
            $table->string('database_port')->default('3306');
            $table->string('database_username');
            $table->string('database_password');
            $table->enum('plan', ['basic', 'standard', 'premium'])->default('basic');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
