<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();
            $table->foreignId('form_source_id')->nullable()->constrained()->nullOnDelete();

            // Normalized contact fields
            $table->string('full_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('budget')->nullable();

            // Tracking fields
            $table->string('page_url')->nullable();
            $table->string('source_url')->nullable();
            $table->string('referrer')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();

            // Raw data
            $table->json('raw_payload')->nullable();
            $table->json('normalized_payload')->nullable();

            // Request metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('country', 2)->nullable();

            // Status and spam
            $table->enum('status', ['new', 'open', 'in_progress', 'closed', 'archived'])->default('new');
            $table->decimal('spam_score', 5, 2)->default(0);
            $table->boolean('is_spam')->default(false);
            $table->boolean('honeypot_triggered')->default(false);

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes for common queries
            $table->index(['workspace_id', 'status']);
            $table->index(['workspace_id', 'created_at']);
            $table->index(['brand_id', 'created_at']);
            $table->index('email');
            $table->index('is_spam');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
