<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('domain'); // e.g. example.com
            $table->string('api_key', 64)->unique();
            $table->json('allowed_origins')->nullable(); // ["https://example.com", "https://www.example.com"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('api_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
