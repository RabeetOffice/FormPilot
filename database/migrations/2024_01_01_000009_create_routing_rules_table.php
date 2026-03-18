<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['service_type', 'brand', 'budget', 'spam_score', 'country', 'fallback']);
            $table->json('conditions')->nullable(); // {"field": "service_type", "operator": "equals", "value": "web_design"}
            $table->foreignId('target_user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('priority')->default(0); // lower = higher priority
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['workspace_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routing_rules');
    }
};
